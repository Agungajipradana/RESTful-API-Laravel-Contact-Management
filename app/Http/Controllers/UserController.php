<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
}
