<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
                    'role' => $user->role,
                    'level' => $user->level,
                    'total_xp' => $user->total_xp,
                ],
                'token' => $token
            ]
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