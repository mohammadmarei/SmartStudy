<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AiQuiz;
use App\Models\UserAnswer;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class QuizController extends Controller
{
    //
    //subject from route and difficulty from query
    public function showByDifficulty(Request $request, $subjectId)
    {
        $difficulty = $request->input('difficulty');//$request->query('difficulty');

        if (!$difficulty) {                                                         
            return response()->json([
                'message' => 'Difficulty is required'
            ], 422);
        }
        $quiz = AiQuiz::with([
            'questions.options' => function ($query) {//شو الاعمدة الي يجيبها
                $query->select('id', 'question_id', 'option_label', 'option_text');
            }
        ])
        ->where('subject_id', $subjectId)
        ->where('difficulty', $difficulty)
        ->first();

        if (!$quiz) {
            return response()->json([
                'message' => 'No quiz found'
            ], 404);
        }

        return response()->json([
            'message' => 'Quiz retrieved successfully',
            'data' => $quiz
        ]);
    }
    public function submitQuiz(Request $request,$quizId)
    {
     $request->validate([
        'answers' => 'required|array|min:1',
        'answers.*.question_id' => 'required|exists:ai_questions,id',//لكل عنصر جوا answer
        'answers.*.option_id' => 'required|exists:question_options,id',
        'start_time' => 'required|date',
    ]);

    $quiz = AiQuiz::with('questions.options')->find($quizId);
    if (!$quiz) {
        return response()->json([
            'message' => 'Quiz not found'
        ], 404);
    }

    $answers = collect($request->input('answers'))->keyBy('question_id');
    $score = 0;
    $correctAnswers = 0;
    $totalQuestions = $quiz->questions->count();
    DB::beginTransaction();

    try {
       $userId=auth()->id();

        $result = Result::create([
            'user_id' => $userId,
            'quiz_id' => $quiz->id,
            'score' => 0,
            'total_questions' => $totalQuestions,
            'correct_answers' => 0,
            'start_time' => $request->start_time,
            'end_time' => now(),
        ]);

        foreach ($quiz->questions as $question) {
            $submittedAnswer = $answers->get($question->id);

            if (!$submittedAnswer) {
                continue;
            }

            $selectedOptionId = (int) $submittedAnswer['option_id'];

           /*  $correctOption = $question->options->firstWhere('is_correct', true);

            $isCorrect = $correctOption && $correctOption->id === $selectedOptionId;//////

            if ($isCorrect) { */
            $selectedOption = $question->options->firstWhere('id', $selectedOptionId);

             if(!$selectedOption) {
                continue;
                    }
            $isCorrect = (bool) $selectedOption->is_correct;
                $score++;
                $correctAnswers++;
            }

            UserAnswer::create([
                'result_id' => $result->id,
                'question_id' => $question->id,
                'option_id' => $selectedOptionId,
                'is_correct' => $isCorrect,
            ]);
        }

        $wrongAnswers = $totalQuestions - $correctAnswers;
        $accuracy = $totalQuestions > 0
            ? round(($correctAnswers / $totalQuestions) * 100)
            : 0;

        $result->update([
            'score' => $score,
            'correct_answers' => $correctAnswers,
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Quiz submitted successfully',
            'data' => [
                'result_id' => $result->id,
                'quiz_id' => $quiz->id,
                'score' => $score,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $wrongAnswers,
                'accuracy' => $accuracy,
            ]
        ]);
    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
        'message' => 'Failed to submit quiz',
        'error' => $e->getMessage(),
        'line' => $e->getLine(),
        'file' => $e->getFile(),
    ], 500);
    }
}


    }