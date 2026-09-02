<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModuleResource;
use App\Models\ArContent;
use App\Models\Challenge;
use App\Models\Materi;
use App\Models\Module;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\UserAnswer;
use App\Models\UserArProgress;
use App\Models\UserMaterialProgress;
use App\Models\UserModuleProgress;
use App\Models\XpTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    /**
     * @group Modul & Materi
     *
     * Daftar Modul
     *
     * Mendapatkan semua daftar modul beserta persentase progres belajarnya.
     *
     * @authenticated
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $modules = Module::with(['userProgress' => function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        }])
            ->orderBy('urutan', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar modul berhasil diambil',
            'data' => ModuleResource::collection($modules),
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Detail Modul
     *
     * Mendapatkan detail satu modul beserta daftar materi dan status penyelesaiannya.
     *
     * @authenticated
     */
    public function show(Request $request, $moduleId)
    {
        $user = $request->user();

        $module = Module::with(['materi' => function ($query) {
            $query->orderBy('urutan', 'asc');
        }])
            ->where('module_id', $moduleId)
            ->firstOrFail();

        $materiProgress = UserMaterialProgress::where('user_id', $user->user_id)
            ->whereIn('materi_id', $module->materi->pluck('materi_id'))
            ->get()
            ->keyBy('materi_id');

        $moduleProgress = UserModuleProgress::where('user_id', $user->user_id)
            ->where('module_id', $module->module_id)
            ->first();

        $materiList = $module->materi->map(function ($item) use ($materiProgress) {
            $progress = $materiProgress->get($item->materi_id);

            return [
                'id' => $item->materi_id,
                'judul' => $item->judul,
                'tipe' => $item->tipe,
                'konten_url' => $item->konten_url,
                'urutan' => $item->urutan,
                'status' => $progress?->status ?? 'belum',
            ];
        })->values();

        return response()->json([
            'status' => 'success',
            'message' => 'Detail modul berhasil diambil',
            'data' => [
                'id' => $module->module_id,
                'kode' => $module->kode_modul,
                'judul' => $module->judul,
                'sub_cpmk' => $module->sub_cpmk,
                'deskripsi' => $module->deskripsi,
                'urutan' => $module->urutan,
                'persen_selesai' => (int) ($moduleProgress?->persen_selesai ?? 0),
                'status' => $moduleProgress?->status ?? 'belum_mulai',
                'materi' => $materiList,
            ],
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Detail Materi
     *
     * Memuat isi konten materi sesuai tipe (teks/video/pdf).
     *
     * @authenticated
     */
    public function detail(Request $request, $materiId)
    {
        $materi = Materi::with('module')->where('materi_id', $materiId)->firstOrFail();

        $progress = UserMaterialProgress::where('user_id', $request->user()->user_id)
            ->where('materi_id', $materi->materi_id)
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Detail materi berhasil diambil',
            'data' => [
                'id' => $materi->materi_id,
                'module_id' => $materi->module_id,
                'judul' => $materi->judul,
                'tipe' => $materi->tipe,
                'konten_url' => $materi->konten_url,
                'urutan' => $materi->urutan,
                'status' => $progress?->status ?? 'belum',
                'selesai_pada' => $progress?->selesai_pada,
                'module' => [
                    'id' => $materi->module->module_id,
                    'judul' => $materi->module->judul,
                    'sub_cpmk' => $materi->module->sub_cpmk,
                ],
            ],
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Tandai Materi Selesai
     *
     * Menyimpan status materi menjadi selesai dan menghitung ulang progres modul.
     *
     * @authenticated
     */
    public function complete(Request $request, $materiId)
    {
        $user = $request->user();

        $materi = Materi::with('module')->where('materi_id', $materiId)->firstOrFail();

        $progress = UserMaterialProgress::updateOrCreate(
            [
                'user_id' => $user->user_id,
                'materi_id' => $materi->materi_id,
            ],
            [
                'status' => 'selesai',
                'selesai_pada' => now(),
            ]
        );

        $this->recalculateModuleProgress($user->user_id, $materi->module_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Materi berhasil ditandai selesai',
            'data' => [
                'materi_id' => $progress->materi_id,
                'module_id' => $materi->module_id,
                'status' => $progress->status,
                'selesai_pada' => $progress->selesai_pada,
                'persen_selesai' => $this->getModuleProgressPercent($user->user_id, $materi->module_id),
            ],
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Daftar AR pada modul
     *
     * Mendapatkan semua data AR yang tersedia di modul tertentu.
     *
     * @authenticated
     */
    public function arList(Request $request, $moduleId)
    {
        $module = Module::where('module_id', $moduleId)->firstOrFail();

        $arItems = ArContent::where('module_id', $module->module_id)
            ->orderBy('urutan', 'asc')
            ->get();

        $progressMap = UserArProgress::where('user_id', $request->user()->user_id)
            ->whereIn('ar_id', $arItems->pluck('ar_id'))
            ->get()
            ->keyBy('ar_id');

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar AR berhasil diambil',
            'data' => $arItems->map(function ($item) use ($progressMap) {
                $progress = $progressMap->get($item->ar_id);

                return [
                    'id' => $item->ar_id,
                    'module_id' => $item->module_id,
                    'judul' => $item->judul,
                    'marker_image_url' => $item->marker_image_url,
                    'model_3d_url' => $item->model_3d_url,
                    'penjelasan' => $item->penjelasan,
                    'urutan' => $item->urutan,
                    'status' => $progress?->status ?? 'belum',
                ];
            })->values(),
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Selesaikan AR
     *
     * Mencatat bahwa user telah berhasil membuka / memindai AR dan memberi XP pada first-open.
     *
     * @authenticated
     */
    public function completeAr(Request $request, $arId)
    {
        $user = $request->user();
        $ar = ArContent::where('ar_id', $arId)->firstOrFail();

        $alreadyOpened = UserArProgress::where('user_id', $user->user_id)
            ->where('ar_id', $ar->ar_id)
            ->exists();

        $progress = $alreadyOpened
            ? UserArProgress::where('user_id', $user->user_id)->where('ar_id', $ar->ar_id)->firstOrFail()
            : new UserArProgress();

        $progress->user_id = $user->user_id;
        $progress->ar_id = $ar->ar_id;
        $progress->status = 'selesai';
        $progress->selesai_pada = now();
        $progress->save();

        $xpAward = 30;
        $xpGiven = 0;

        $hasXpTransaction = XpTransaction::where('user_id', $user->user_id)
            ->where('source_type', 'ar_explore')
            ->where('source_id', $ar->ar_id)
            ->exists();

        if (!$alreadyOpened && !$hasXpTransaction) {
            $transactionId = (string) Str::uuid();

            XpTransaction::create([
                'transaction_id' => $transactionId,
                'user_id' => $user->user_id,
                'source_type' => 'ar_explore',
                'source_id' => $ar->ar_id,
                'jumlah_xp' => $xpAward,
                'created_at' => now(),
            ]);

            $user->total_xp = (int) $user->total_xp + $xpAward;
            $user->save();
            $xpGiven = $xpAward;
        }

        return response()->json([
            'status' => 'success',
            'message' => 'AR berhasil ditandai selesai',
            'data' => [
                'ar_id' => $ar->ar_id,
                'module_id' => $ar->module_id,
                'status' => $progress->status,
                'selesai_pada' => $progress->selesai_pada,
                'xp_diberikan' => $xpGiven,
            ],
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Daftar challenge pada modul
     *
     * @authenticated
     */
    public function challengeList(Request $request, $moduleId)
    {
        $module = Module::where('module_id', $moduleId)->firstOrFail();

        $challenges = Challenge::where('module_id', $module->module_id)
            ->orderBy('challenge_id', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar challenge berhasil diambil',
            'data' => $challenges->map(function ($challenge) {
                return [
                    'id' => $challenge->challenge_id,
                    'module_id' => $challenge->module_id,
                    'judul' => $challenge->judul,
                    'jumlah_soal' => $challenge->jumlah_soal,
                ];
            })->values(),
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Daftar pertanyaan challenge
     *
     * @authenticated
     */
    public function questionList(Request $request, $challengeId)
    {
        $challenge = Challenge::with(['questions.options' => function ($query) {
            $query->orderBy('option_id', 'asc');
        }])->where('challenge_id', $challengeId)->firstOrFail();

        $questions = $challenge->questions()->orderBy('urutan', 'asc')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar pertanyaan berhasil diambil',
            'data' => [
                'challenge_id' => $challenge->challenge_id,
                'module_id' => $challenge->module_id,
                'judul' => $challenge->judul,
                'jumlah_soal' => $challenge->jumlah_soal,
                'questions' => $questions->map(function ($question) {
                    return [
                        'id' => $question->question_id,
                        'pertanyaan' => $question->pertanyaan,
                        'urutan' => $question->urutan,
                        'xp_reward' => $question->xp_reward,
                        'options' => $question->options->map(function ($option) {
                            return [
                                'id' => $option->option_id,
                                'teks_opsi' => $option->teks_opsi,
                            ];
                        })->values(),
                    ];
                })->values(),
            ],
        ]);
    }

    /**
     * @group Modul & Materi
     *
     * Ringkasan progress modul
     *
     * @authenticated
     */
    public function progressSummary(Request $request, $moduleId)
    {
        $user = $request->user();
        $module = Module::with(['materi', 'arContents', 'challenges.questions'])
            ->where('module_id', $moduleId)
            ->firstOrFail();

        $totalMateri = $module->materi->count();
        $materiSelesai = UserMaterialProgress::where('user_id', $user->user_id)
            ->whereIn('materi_id', $module->materi->pluck('materi_id'))
            ->where('status', 'selesai')
            ->count();

        $totalAr = $module->arContents->count();
        $arSelesai = UserArProgress::where('user_id', $user->user_id)
            ->whereIn('ar_id', $module->arContents->pluck('ar_id'))
            ->where('status', 'selesai')
            ->count();

        $totalChallenges = $module->challenges->sum(function ($challenge) {
            return max((int) $challenge->jumlah_soal, $challenge->questions()->count());
        });

        $challengeDikerjakan = UserAnswer::where('user_id', $user->user_id)
            ->whereIn('question_id', $module->challenges->flatMap(fn($challenge) => $challenge->questions->pluck('question_id'))->values())
            ->distinct('question_id')
            ->count('question_id');

        $moduleProgress = UserModuleProgress::where('user_id', $user->user_id)
            ->where('module_id', $module->module_id)
            ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Progress modul berhasil diambil',
            'data' => [
                'module_id' => $module->module_id,
                'persen_selesai' => (int) ($moduleProgress?->persen_selesai ?? 0),
                'materi' => [
                    'selesai' => $materiSelesai,
                    'total' => $totalMateri,
                    'rasio' => $totalMateri > 0 ? $materiSelesai . '/' . $totalMateri : '0/0',
                ],
                'ar' => [
                    'selesai' => $arSelesai,
                    'total' => $totalAr,
                    'rasio' => $totalAr > 0 ? $arSelesai . '/' . $totalAr : '0/0',
                ],
                'challenge' => [
                    'dikerjakan' => $challengeDikerjakan,
                    'total' => $totalChallenges,
                    'rasio' => $totalChallenges > 0 ? $challengeDikerjakan . '/' . $totalChallenges : '0/0',
                ],
                'skor_rata_rata' => (float) ($moduleProgress?->skor_rata_rata ?? 0),
            ],
        ]);
    }

    private function recalculateModuleProgress(string $userId, int $moduleId): void
    {
        $module = Module::with('materi')->where('module_id', $moduleId)->firstOrFail();

        $totalMateri = $module->materi->count();

        if ($totalMateri === 0) {
            $persen = 0;
            $status = 'belum_mulai';
        } else {
            $completed = UserMaterialProgress::where('user_id', $userId)
                ->whereIn('materi_id', $module->materi->pluck('materi_id'))
                ->where('status', 'selesai')
                ->count();

            $persen = (int) round(($completed / $totalMateri) * 100);
            $status = $persen >= 100 ? 'selesai' : ($persen > 0 ? 'berjalan' : 'belum_mulai');
        }

        UserModuleProgress::updateOrCreate(
            [
                'user_id' => $userId,
                'module_id' => $moduleId,
            ],
            [
                'persen_selesai' => $persen,
                'status' => $status,
            ]
        );
    }

    private function getModuleProgressPercent(string $userId, int $moduleId): int
    {
        $progress = UserModuleProgress::where('user_id', $userId)
            ->where('module_id', $moduleId)
            ->first();

        return (int) ($progress?->persen_selesai ?? 0);
    }
}
