<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Result;
use App\Models\UserAnswer;
class ResultController extends Controller
{
    //
    public function getResults($resultId)
    {
        // Logic to retrieve results based on the result ID
        $result=Result::find($resultId);
        if (!$result) {
            return response()->json([
                'message' => 'Result not found'
            ], 404);
        }
        $wrongAnswers = $result->total_questions - $result->correct_answers;
        $accuracy = $result->score / $result->total_questions * 100;
            return response()->json([
            'message' => 'Result retrieved successfully',
            'data' => [
                'id' => $result->id,
                'quiz_id' => $result->quiz_id,
                'quiz_title' => $result->quiz?->title,
                'difficulty' => $result->quiz?->difficulty,
                'score' => $result->score,
                'total_questions' => $result->total_questions,
                'correct_answers' => $result->correct_answers,
                'wrong_answers' => $wrongAnswers,
                'accuracy' => $accuracy,
                'start_time' => $result->start_time,
                'end_time' => $result->end_time,
            ]
        ]);

    }
    public function getResultDetails($resultId)
    {
        // Logic to retrieve detailed results based on the result ID
            $result = Result::with([
                    'quiz',
                    'userAnswers.question.options',
                    'userAnswers.option'
                ])->find($resultId);
        if (!$result) {
            return response()->json([
                'message' => 'Result not found'
            ], 404);
        }
        $answers = $result->userAnswers->map(function ($userAnswer) {
        $question = $userAnswer->question;

        $correctOption = $question->options->firstWhere('is_correct', true);

        return [
            'question_id' => $question->id,
            'question_text' => $question->question_text,
            'explanation' => $question->explanation,

            'selected_option_id' => $userAnswer->option?->id,
            'selected_option_label' => $userAnswer->option?->option_label,
            'selected_option_text' => $userAnswer->option?->option_text,

            'correct_option_id' => $correctOption?->id,
            'correct_option_label' => $correctOption?->option_label,
            'correct_option_text' => $correctOption?->option_text,

            'is_correct' => (bool) $userAnswer->is_correct,
        ];
    });
        return response()->json([
        'message' => 'Result details retrieved successfully',
        'data' => [
            'result_id' => $result->id,
            'quiz_id' => $result->quiz_id,
            'quiz_title' => $result->quiz?->title,
            'difficulty' => $result->quiz?->difficulty,
            'answers' => $answers,
        ]
    ]);
    }

}
