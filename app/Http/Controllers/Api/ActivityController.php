<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailyActivityLog;
use App\Models\Recommendation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityController extends Controller
{
    public function summary(Request $request)
    {
        $userId = $request->user()->user_id;
        $startDate = Carbon::today()->subDays(6);
        $logs = DailyActivityLog::where('user_id', $userId)
            ->whereBetween('tanggal', [$startDate->toDateString(), Carbon::today()->toDateString()])
            ->get()
            ->keyBy(fn(DailyActivityLog $log) => $log->tanggal->toDateString());

        $hari = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        $grafik = collect(range(0, 6))->map(function (int $offset) use ($startDate, $logs, $hari) {
            $date = $startDate->copy()->addDays($offset);
            $log = $logs->get($date->toDateString());

            return [
                'tanggal' => $date->toDateString(),
                'hari' => $hari[$date->dayOfWeekIso],
                'waktu_belajar_menit' => $log?->waktu_belajar_menit ?? 0,
                'ar_explore_count' => $log?->ar_explore_count ?? 0,
                'challenge_count' => $log?->challenge_count ?? 0,
            ];
        });

        $recommendations = Recommendation::with('module')
            ->where('user_id', $userId)
            ->latest('created_at')
            ->limit(10)
            ->get()
            ->map(fn(Recommendation $recommendation) => [
                'id' => $recommendation->id,
                'teks' => $recommendation->teks,
                'is_read' => $recommendation->is_read,
                'created_at' => $recommendation->created_at,
                'module' => $recommendation->module ? [
                    'module_id' => $recommendation->module->module_id,
                    'kode_modul' => $recommendation->module->kode_modul,
                    'judul' => $recommendation->module->judul,
                ] : null,
            ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Aktivitas belajar berhasil diambil',
            'data' => [
                'grafik_harian' => $grafik,
                'rekomendasi' => $recommendations,
            ],
        ]);
    }

    public function logTime(Request $request)
    {
        $validated = $request->validate([
            'waktu_belajar_menit' => ['required', 'integer', 'min:1', 'max:1440'],
        ]);

        $log = DailyActivityLog::firstOrCreate(
            [
                'user_id' => $request->user()->user_id,
                'tanggal' => Carbon::today()->toDateString(),
            ],
            [
                'id' => (string) Str::uuid(),
                'waktu_belajar_menit' => 0,
                'ar_explore_count' => 0,
                'challenge_count' => 0,
            ]
        );

        $log->increment('waktu_belajar_menit', $validated['waktu_belajar_menit']);
        $log->refresh();

        return response()->json([
            'status' => 'success',
            'message' => 'Waktu belajar berhasil dicatat',
            'data' => [
                'tanggal' => $log->tanggal->toDateString(),
                'waktu_belajar_menit' => $log->waktu_belajar_menit,
            ],
        ]);
    }
}
