<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Kelas;

class AuthController extends Controller
{
    /**
     * Login Pengguna
     *
     * Endpoint ini digunakan untuk melakukan autentikasi mahasiswa atau admin menggunakan email dan password. 
     * Respons akan mengembalikan Token Sanctum (Bearer) yang harus disertakan di endpoint lain.
     * 
     * @unauthenticated
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

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
                    'role' => $user->role,
                    'level' => $user->level,
                    'total_xp' => $user->total_xp,
                ],
                'token' => $token
            ]
        ], 200);
    }
    /**
     * Registrasi Pengguna Baru
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'kelas_id' => 'required|exists:kelas,kelas_id'
        ]);

        // Validasi wajib email um.ac.id atau student.um.ac.id
        if (!str_ends_with($request->email, '@um.ac.id') && !str_ends_with($request->email, '@student.um.ac.id')) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran gagal. Anda wajib menggunakan email domain @um.ac.id atau @student.um.ac.id!'
            ], 422);
        }

        $user = User::create([
            'user_id' => (string) Str::uuid(), // Membutuhkan UUID sesuai schema database Anda[cite: 9]
            'kelas_id' => $request->kelas_id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password), // Password disimpan ke password_hash[cite: 9, 16]
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
                'token' => $token
            ]
        ], 201);
    }

    /**
     * Mengambil Daftar Kelas untuk Dropdown Flutter
     */
    public function getKelas()
    {
        $kelas = Kelas::select('kelas_id', 'nama_kelas', 'tahun_ajaran')->get();
        return response()->json([
            'success' => true,
            'data' => $kelas
        ], 200);
    }
    /**
     * Logout Pengguna
     *
     * Endpoint ini digunakan untuk menghapus sesi/token pengguna yang sedang aktif (Revoke Token).
     * Membutuhkan Bearer Token di header Authorization.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil!'
        ], 200);
    }
}
