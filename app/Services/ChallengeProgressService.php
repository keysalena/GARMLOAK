<?php

namespace App\Services;

use App\Models\Module;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserModuleProgress;
use App\Models\XpTransaction;
use Illuminate\Support\Str;

class ChallengeProgressService
{
    public function submitAnswer(User $user, Question $question, int $optionId): array
    {
        $option = $question->options()->where('option_id', $optionId)->firstOrFail();

        $isCorrect = (bool) $option->is_correct;
        $xpDidapat = $isCorrect ? (int) $question->xp_reward : 0;

        UserAnswer::create([
            'answer_id' => (string) Str::uuid(),
            'user_id' => $user->user_id,
            'question_id' => $question->question_id,
            'option_id' => $option->option_id,
            'is_correct' => $isCorrect,
            'xp_didapat' => $xpDidapat,
            'dijawab_pada' => now(),
        ]);

        if ($isCorrect) {
            $user->total_xp = (int) $user->total_xp + $xpDidapat;
            $user->save();

            XpTransaction::create([
                'transaction_id' => (string) Str::uuid(),
                'user_id' => $user->user_id,
                'source_type' => 'challenge',
                'source_id' => $question->question_id,
                'jumlah_xp' => $xpDidapat,
                'created_at' => now(),
            ]);
        }

        $this->updateModuleScore($user->user_id, $question->challenge->module_id);

        return [
            'is_correct' => $isCorrect,
            'xp_didapat' => $xpDidapat,
            'penjelasan' => $question->penjelasan ?? ($isCorrect
                ? 'Jawaban Anda benar.'
                : 'Jawaban Anda belum tepat. Silakan pelajari kembali materi pembahasan terkait.'),
        ];
    }

    protected function updateModuleScore(string $userId, int $moduleId): void
    {
        $module = Module::with('challenges.questions')->where('module_id', $moduleId)->firstOrFail();

        $questions = $module->challenges
            ->flatMap(fn($challenge) => $challenge->questions)
            ->values();

        $totalQuestions = $questions->count();

        if ($totalQuestions === 0) {
            $skor = 0;
        } else {
            $totalReward = $questions->sum(fn($question) => (int) $question->xp_reward);
            $earnedReward = UserAnswer::where('user_id', $userId)
                ->whereIn('question_id', $questions->pluck('question_id'))
                ->where('is_correct', true)
                ->sum('xp_didapat');

            $skor = $totalReward > 0 ? round(($earnedReward / $totalReward) * 100, 2) : 0;
        }

        UserModuleProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'module_id' => $moduleId,
            ],
            [
                'skor_rata_rata' => $skor,
            ]
        );
    }
}
