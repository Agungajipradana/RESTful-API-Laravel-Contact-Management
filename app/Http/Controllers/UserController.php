<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // Method untuk melakukan registrasi user baru
    public function register(UserRegisterRequest $request): JsonResponse
    {
        // Memvalidasi inputan dengan aturan yang telah ditentukan
        $data = $request->validated();

        // Memeriksa apakah username sudah terdaftar sebelumnya
        if (User::where("username", $data["username"])->count() == 1) {
            throw new HttpResponseException(response([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ], 400));
        }

        // Membuat instance model User dengan data yang valid
        $user = new User($data);
        // Mengenkripsi password sebelum disimpan
        $user->password = Hash::make($data["password"]);
        // Menyimpan user baru ke database
        $user->save();

        // Mengembalikan response berupa data user yang baru saja dibuat dengan status code 201 (Created)
        return(new UserResource($user))->response()->setStatusCode(201);
    }

    // Method untuk melakukan login user
    public function login(UserLoginRequest $requuest): UserResource
    {
        // Memvalidasi inputan dengan aturan yang telah ditentukan
        $data = $requuest->validated();

        // Mencari user berdasarkan username
        $user = User::where("username", $data["username"])->first();

        // Memeriksa apakah user ditemukan dan password cocok
        if (!$user || !Hash::check($data["password"], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ], 401));
        }

        // Membuat token baru untuk user dan menyimpannya ke database
        $user->token = Str::uuid()->toString();
        $user->save();

        // Mengembalikan response berupa data user dengan token baru
        return new UserResource($user);
    }

    // Method untuk mendapatkan data user yang sedang login
    public function get(Request $request): UserResource
    {
        // Mendapatkan user yang sedang login
        $user = Auth::user();
        // Mengembalikan response berupa data user
        return new UserResource($user);
    }
}
