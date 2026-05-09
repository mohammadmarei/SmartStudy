<?php

namespace App\Services;

use App\Models\AiJob;
use App\Models\AiKeySummary;
use App\Models\AiQuestion;
use App\Models\AiQuiz;
use App\Models\AiSummary;
use App\Models\File;
use App\Models\QuestionOption;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AiContentGenerator
{
    public function __construct(
        private readonly AnthropicClient $anthropic,
        private readonly MaterialTextExtractor $extractor,
    ) {}

    /**
     * @return array{job:AiJob, summary:AiSummary, key_points:array<int,AiKeySummary>}
     */
    public function generateSummary(Subject $subject, File $material, int $keyPoints = 10, ?string $model = null): array
    {
        $text = $this->extractor->extract($material);
        $text = $this->capText($text);

        $job = null;
        $summary = null;
        $keyRows = [];

        DB::transaction(function () use ($subject, $material, $model, $keyPoints, $text, &$job, &$summary, &$keyRows) {
            $job = AiJob::create([
                'material_id' => $material->id,
                'subject_id' => $subject->id,
                'user_id' => $subject->user_id,
                'model_used' => $model ?? config('services.anthropic.model'),
                'status' => 'processing',
                'error_message' => null,
                'retry_count' => 0,
                'created_at' => now(),
                'completed_at' => null,
            ]);

            try {
                $out = $this->anthropic->messages(
                    [
                        [
                            'role' => 'user',
                            'content' => $this->summaryPrompt($subject->name, $text, $keyPoints),
                        ],
                    ],
                    [
                        'model' => $job->model_used,
                        'max_tokens' => 1400,
                        'temperature' => 0.2,
                    ]
                );

                $parsed = $this->parseJson($out);

                $summary = AiSummary::create([
                    'job_id' => $job->id,
                    'summary_text' => (string) ($parsed['summary'] ?? ''),
                ]);

                $items = is_array($parsed['key_points'] ?? null) ? $parsed['key_points'] : [];
                foreach (array_slice($items, 0, $keyPoints) as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $concept = trim((string) ($item['concept'] ?? ''));
                    $type = (string) ($item['type'] ?? 'concept');
                    $type = in_array($type, ['concept', 'keyword'], true) ? $type : 'concept';
                    if ($concept === '') {
                        continue;
                    }
                    $keyRows[] = AiKeySummary::create([
                        'summary_id' => $summary->id,
                        'concept' => Str::limit($concept, 255, ''),
                        'concept_type' => $type,
                    ]);
                }

                $job->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'error_message' => null,
                ]);
            } catch (\Throwable $e) {
                $job->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'error_message' => Str::limit($e->getMessage(), 1000),
                ]);
                throw $e;
            }
        });

        return [
            'job' => $job,
            'summary' => $summary,
            'key_points' => $keyRows,
        ];
    }

    /**
     * @return array{job:AiJob, quiz:AiQuiz}
     */
    public function generateQuiz(
        Subject $subject,
        File $material,
        string $difficulty = 'medium',
        int $questionCount = 10,
        ?string $model = null
    ): array {
        $text = $this->extractor->extract($material);
        $text = $this->capText($text);

        $difficulty = in_array($difficulty, ['easy', 'medium', 'hard'], true) ? $difficulty : 'medium';
        $questionCount = max(1, min(25, $questionCount));

        $job = null;
        $quiz = null;

        DB::transaction(function () use ($subject, $material, $model, $difficulty, $questionCount, $text, &$job, &$quiz) {
            $job = AiJob::create([
                'material_id' => $material->id,
                'subject_id' => $subject->id,
                'user_id' => $subject->user_id,
                'model_used' => $model ?? config('services.anthropic.model'),
                'status' => 'processing',
                'error_message' => null,
                'retry_count' => 0,
                'created_at' => now(),
                'completed_at' => null,
            ]);

            try {
                $out = $this->anthropic->messages(
                    [
                        [
                            'role' => 'user',
                            'content' => $this->quizPrompt($subject->name, $text, $difficulty, $questionCount),
                        ],
                    ],
                    [
                        'model' => $job->model_used,
                        'max_tokens' => 1800,
                        'temperature' => 0.3,
                    ]
                );

                $parsed = $this->parseJson($out);
                $timeLimit = max(5, $questionCount * 2);
                $quiz = AiQuiz::create([
                   'job_id' => $job->id,
                    'subject_id' => $subject->id,
                    'content_id' => $material->id,
                    'title' => $subject->name . ' Quiz - ' . ucfirst($difficulty),
                    'difficulty' => $difficulty,
                    'time_limit' => $timeLimit,
                ]);

                $questions = is_array($parsed['questions'] ?? null) ? $parsed['questions'] : [];
                foreach (array_slice($questions, 0, $questionCount) as $q) {
                    if (!is_array($q)) {
                        continue;
                    }

                    $questionText = trim((string) ($q['question'] ?? ''));
                    if ($questionText === '') {
                        continue;
                    }

                    /** @var AiQuestion $question */
                    $question = AiQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => $questionText,
                        'explanation' => isset($q['explanation']) ? (string) $q['explanation'] : null,
                    ]);

                    $options = is_array($q['options'] ?? null) ? $q['options'] : [];
                    $correct = strtoupper(trim((string) ($q['correct'] ?? '')));

                    foreach ($options as $opt) {
                        if (!is_array($opt)) {
                            continue;
                        }

                        $label = strtoupper(trim((string) ($opt['label'] ?? '')));
                        $text = trim((string) ($opt['text'] ?? ''));
                        if ($label === '' || $text === '') {
                            continue;
                        }

                        QuestionOption::create([
                            'question_id' => $question->id,
                            'option_label' => Str::limit($label, 5, ''),
                            'option_text' => $text,
                            'is_correct' => $label === $correct,
                        ]);
                    }
                }

                $job->update([
                    'status' => 'completed',
                    'completed_at' => now(),
                    'error_message' => null,
                ]);
            } catch (\Throwable $e) {
                $job->update([
                    'status' => 'failed',
                    'completed_at' => now(),
                    'error_message' => Str::limit($e->getMessage(), 1000),
                ]);
                throw $e;
            }
        });

        return [
            'job' => $job,
            'quiz' => $quiz,
        ];
    }

    private function capText(string $text): string
    {
        $text = preg_replace('/\s+/', ' ', $text) ?? $text;
        return Str::limit(trim($text), 18000, '...');
    }

    private function parseJson(string $raw): array
    {
        $raw = trim($raw);
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{[\s\S]*\}/', $raw, $m)) {
            $decoded = json_decode($m[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw new \RuntimeException('Model output was not valid JSON.');
    }

    private function summaryPrompt(string $subjectName, string $materialText, int $keyPoints): string
    {
        return <<<PROMPT
You are an expert tutor.

Given the study material text below for the subject "{$subjectName}", produce a concise, accurate study summary and a list of key concepts/keywords.

Return ONLY valid JSON in this exact shape:
{
  "summary": "string",
  "key_points": [
    {"concept": "string", "type": "concept|keyword"}
  ]
}

Constraints:
- summary: 150-250 words
- key_points: up to {$keyPoints} items
- No markdown, no extra keys, no extra text outside JSON.

MATERIAL:
{$materialText}
PROMPT;
    }

    private function quizPrompt(string $subjectName, string $materialText, string $difficulty, int $questionCount): string
    {
        return <<<PROMPT
You are an expert teacher.

Create a multiple-choice quiz from the study material for the subject "{$subjectName}".

Difficulty: {$difficulty}
Number of questions: {$questionCount}

Return ONLY valid JSON in this exact shape:
{
  "questions": [
    {
      "question": "string",
      "options": [
        {"label": "A", "text": "string"},
        {"label": "B", "text": "string"},
        {"label": "C", "text": "string"},
        {"label": "D", "text": "string"}
      ],
      "correct": "A|B|C|D",
      "explanation": "string"
    }
  ]
}

Constraints:
- Exactly 4 options per question (A-D)
- Exactly 1 correct option
- No markdown, no extra keys, no extra text outside JSON.

MATERIAL:
{$materialText}
PROMPT;
    }
}

