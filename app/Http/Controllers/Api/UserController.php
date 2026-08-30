<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        // $request->user() mengambil data user yang sedang login via token
        // load() digunakan untuk Eager Loading relasi yang dibutuhkan
        $user = $request->user()->load(['kelas', 'levelInfo']);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }
}