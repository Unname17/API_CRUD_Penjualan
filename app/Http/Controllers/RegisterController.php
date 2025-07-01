<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class RegisterController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        // Validasi input
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        try {
            // Buat user baru
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Akun pengguna berhasil ditambahkan.',
                'data' => new UserResource($user)
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal membuat akun pengguna.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
