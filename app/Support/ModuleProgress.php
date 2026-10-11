<?php

namespace App\Support;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Menghitung progres materi SEMUA modul sekaligus dengan 2 query,
 * menggantikan 2 query per modul (N+1) yang sebelumnya ada di
 * HomeController dan ModuleController.
 */
class ModuleProgress
{
    /**
     * @return array{0: Collection, 1: Collection}
     *         [total materi per module_id, materi selesai user per module_id]
     */
    public static function countsForUser(string $userId): array
    {
        $totals = DB::table('materi')
            ->where('tipe', '!=', 'pendahuluan')
            ->selectRaw('module_id, COUNT(*) as total')
            ->groupBy('module_id')
            ->pluck('total', 'module_id');

        $done = DB::table('user_progress as up')
            ->join('materi as m', 'up.materi_id', '=', 'm.materi_id')
            ->where('up.user_id', $userId)
            ->where('m.tipe', '!=', 'pendahuluan')
            ->selectRaw('m.module_id, COUNT(*) as total')
            ->groupBy('m.module_id')
            ->pluck('total', 'module_id');

        return [$totals, $done];
    }

    /** Progres 0.0 - 1.0 untuk satu modul. */
    public static function ratio(int $total, int $done): float
    {
        return $total > 0 ? min(1.0, $done / $total) : 0.0;
    }

    /** Level dari total XP (kelipatan 500 XP = 1 level). */
    public static function levelFor(int $totalXp): int
    {
        return intdiv($totalXp, 500) + 1;
    }
}