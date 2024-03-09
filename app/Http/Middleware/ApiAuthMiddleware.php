<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

// Middleware untuk mengautentikasi request API
class ApiAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle(Request $request, Closure $next): Response
    {
        // Mengambil token dari header Authorization
        $token = $request->header("Authorization");
        // Inisialisasi variabel untuk menandakan status autentikasi
        $authenticate = true;

        // Jika token tidak ada, set autentikasi ke false
        if (!$token) {
            $authenticate = false;
        }

        // Mencari user berdasarkan token
        $user = User::where("token", $token)->first();

        // Jika user tidak ditemukan, set autentikasi ke false
        if (!$user) {
            $authenticate = false;
        } else {
            Auth::login($user);
        }

        // Jika autentikasi berhasil, lanjutkan ke request selanjutnya
        if ($authenticate) {
            return $next($request);
        } else {
            // Jika autentikasi gagal, kirim response JSON dengan pesan unauthorized
            return response()->json([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ])->setStatusCode(401);
        }
    }
}
