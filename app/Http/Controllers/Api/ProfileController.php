<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Wajib ditambahkan untuk query builder
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Mengambil Data Profil Pengguna
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();

        // Memuat data dari tabel kelas terkait
        $user->loadMissing('kelas');

        return response()->json([
            'success' => true,
            'message' => 'Data profil berhasil diambil',
            'data' => [
                'nama' => $user->nama,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url ? url('storage/' . $user->avatar_url) : null,
                'level' => $user->level ?? 1,
                'xp' => $user->total_xp ?? 0,
                'xp_target' => ($user->level ?? 1) * 500,
                // Menambahkan nama kelas (atau teks default jika null)
                'nama_kelas' => $user->kelas ? $user->kelas->nama_kelas : 'Belum ada kelas',
                'bergabung_sejak' => $user->created_at->translatedFormat('d F Y')
            ]
        ], 200);
    }

    /**
     * Memperbarui Foto Profil
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        $user = $request->user();

        if ($request->hasFile('avatar')) {
            // Hapus foto lama jika ada (opsional)
            if ($user->avatar_url && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->avatar_url)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar_url);
            }

            // Simpan foto baru ke folder storage/app/public/avatars
            $path = $request->file('avatar')->store('avatars', 'public');

            // Simpan path ke database
            $user->avatar_url = $path;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Foto profil berhasil diperbarui',
                'avatar_url' => url('storage/' . $path)
            ], 200);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada gambar yang diunggah'], 400);
    }
    /**
     * Mengambil Data Statistik / Progress Belajar
     */
    public function getProgress(Request $request)
    {
        $user = $request->user();

        // 1. Hitung Total Modul Keseluruhan
        $totalModules = Module::count();

        // 2. Hitung Total Materi Keseluruhan di Aplikasi (Abaikan pendahuluan)
        $totalMateri = DB::table('materi')->where('tipe', '!=', 'pendahuluan')->count();

        // 3. Hitung Total Materi yang Selesai Dikerjakan User Ini (Abaikan pendahuluan)
        $materiSelesai = DB::table('user_progress')
            ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
            ->where('user_progress.user_id', $user->user_id) // Sesuaikan user_id atau id
            ->where('materi.tipe', '!=', 'pendahuluan')
            ->count();

        // 4. Hitung Persentase Akurat (0 - 100)
        $persentaseKeseluruhan = $totalMateri > 0 ? round(($materiSelesai / $totalMateri) * 100) : 0;

        // 5. Hitung Jumlah Modul yang Benar-Benar 100% Selesai
        $modules = DB::table('modules')->get();
        $modulSelesaiCount = 0;

        foreach ($modules as $mod) {
            // Berapa target materi reguler di modul ini?
            $jmlMateriModul = DB::table('materi')
                ->where('module_id', $mod->module_id)
                ->where('tipe', '!=', 'pendahuluan')
                ->count();

            // Berapa materi reguler di modul ini yang sudah di-klik selesai oleh user?
            $jmlMateriUserModul = DB::table('user_progress')
                ->join('materi', 'user_progress.materi_id', '=', 'materi.materi_id')
                ->where('user_progress.user_id', $user->user_id)
                ->where('materi.module_id', $mod->module_id)
                ->where('materi.tipe', '!=', 'pendahuluan')
                ->count();

            // Jika modul memiliki materi (tidak kosong) DAN jumlah materi yg selesai SAMA DENGAN target materi
            if ($jmlMateriModul > 0 && $jmlMateriModul == $jmlMateriUserModul) {
                $modulSelesaiCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data progress berhasil diambil',
            'data' => [
                'total_xp' => $user->total_xp ?? 0,
                'level' => $user->level ?? 1,
                'modul_selesai' => $modulSelesaiCount,
                'total_modul' => $totalModules,
                'persentase_keseluruhan' => $persentaseKeseluruhan,
            ]
        ], 200);
    }
    /**
     * Memperbarui Data Profil Pengguna
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        if (!str_ends_with($request->email, '@um.ac.id') && !str_ends_with($request->email, '@student.um.ac.id')) {
            return response()->json([
                'success' => false,
                'message' => 'Anda wajib menggunakan email domain @um.ac.id atau @student.um.ac.id!'
            ], 422);
        }

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui'
        ], 200);
    }
    /**
     * Memperbarui Kata Sandi
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed', // 'confirmed' mensyaratkan input 'new_password_confirmation' yang identik
        ]);

        $user = $request->user();

        // Cek apakah password lama yang dimasukkan sesuai dengan yang ada di database
        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi saat ini tidak cocok.'
            ], 400);
        }

        // Simpan password baru
        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui.'
        ], 200);
    }
}
