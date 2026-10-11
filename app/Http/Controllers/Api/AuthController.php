<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Login Pengguna
     *
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // with('kelas') = 1 query tambahan saja
        $user = User::with('kelas')->where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau Password salah!',
            ], 401);
        }

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil!',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                    'bergabung_sejak' => Carbon::parse($user->created_at)->translatedFormat('F Y'),
                    'nama_kelas' => $user->kelas?->nama_kelas ?? 'Belum ada kelas',
                    'role' => $user->role,
                    'level' => $user->level,
                    'total_xp' => $user->total_xp,
                ],
                'token' => $token,
            ],
        ], 200);
    }

    /**
     * Registrasi Pengguna Baru
     *
     * @unauthenticated
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'kelas_id' => 'required|exists:kelas,kelas_id',
        ]);

        if (!str_ends_with($request->email, '@um.ac.id') && !str_ends_with($request->email, '@student.um.ac.id')) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran gagal. Anda wajib menggunakan email domain @um.ac.id atau @student.um.ac.id!',
            ], 422);
        }

        $user = User::create([
            'user_id' => (string) Str::uuid(),
            'kelas_id' => $request->kelas_id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'role' => 'mahasiswa',
            'level' => 1,
            'total_xp' => 0,
        ]);

        $token = $user->createToken('mobile_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil!',
            'data' => [
                'user' => [
                    'user_id' => $user->user_id,
                    'nama' => $user->nama,
                    'email' => $user->email,
                ],
                'token' => $token,
            ],
        ], 201);
    }

    /**
     * Daftar kelas untuk dropdown register. Jarang berubah, di-cache 1 jam.
     * Setelah menambah/mengubah kelas, jalankan: php artisan cache:forget kelas_list
     *
     * @unauthenticated
     */
    public function getKelas()
    {
        $kelas = Cache::remember('kelas_list', 3600, fn () =>
            Kelas::select('kelas_id', 'nama_kelas', 'tahun_ajaran')->get()->toArray()
        );

        return response()->json([
            'success' => true,
            'data' => $kelas,
        ], 200);
    }

    /**
     * Logout (cabut token yang sedang dipakai).
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!',
        ], 200);
    }
}