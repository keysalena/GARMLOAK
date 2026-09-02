<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Services\ChallengeProgressService;
use Illuminate\Http\Request;

class QuestionAnswerController extends Controller
{
    public function __construct(protected ChallengeProgressService $progressService) {}

    public function submit(Request $request, string $questionId)
    {
        $request->validate([
            'option_id' => ['required', 'integer', 'exists:question_options,option_id'],
        ]);

        $question = Question::with(['challenge.module', 'options'])
            ->where('question_id', $questionId)
            ->firstOrFail();

        $optionId = (int) $request->input('option_id');

        if (!$question->options()->where('option_id', $optionId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Pilihan jawaban tidak valid untuk pertanyaan ini.',
            ], 422);
        }

        $result = $this->progressService->submitAnswer($request->user(), $question, $optionId);

        return response()->json([
            'status' => 'success',
            'message' => 'Jawaban berhasil diproses',
            'data' => [
                'question_id' => $question->question_id,
                'is_correct' => $result['is_correct'],
                'xp_didapat' => $result['xp_didapat'],
                'penjelasan' => $result['penjelasan'],
            ],
        ]);
    }
}
