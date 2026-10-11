<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Data profil (dipakai profile_screen.dart lewat GET /user/profile).
     */
    public function getProfile(Request $request)
    {
        $user = $request->user();
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
                'nama_kelas' => $user->kelas?->nama_kelas ?? 'Belum ada kelas',
                'bergabung_sejak' => $user->created_at,
            ],
        ], 200);
    }

    /**
     * Update foto profil.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // maks 2MB
        ]);

        $user = $request->user();
        $disk = Storage::disk('public');

        // Hapus foto lama
        if ($user->avatar_url && $disk->exists($user->avatar_url)) {
            $disk->delete($user->avatar_url);
        }

        $path = $request->file('avatar')->store('avatars', 'public');

        $user->avatar_url = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diperbarui',
            'avatar_url' => url('storage/' . $path),
        ], 200);
    }

    /**
     * Update nama & email.
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
                'message' => 'Anda wajib menggunakan email domain @um.ac.id atau @student.um.ac.id!',
            ], 422);
        }

        $user->nama = $request->nama;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui',
        ], 200);
    }

    /**
     * Ganti kata sandi.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Kata sandi saat ini tidak cocok.',
            ], 400);
        }

        $user->password_hash = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui.',
        ], 200);
    }
}