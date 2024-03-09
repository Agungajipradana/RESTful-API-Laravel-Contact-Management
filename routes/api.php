<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route untuk registrasi user baru
Route::post("/users", [App\Http\Controllers\UserController::class, "register"]);

// Route untuk login user
Route::post("/users/login", [App\Http\Controllers\UserController::class, "login"]);

// Grouping route dengan middleware ApiAuthMiddleware untuk memastikan user telah login
Route::middleware(\App\Http\Middleware\ApiAuthMiddleware::class)->group(function () {
    // Route untuk mendapatkan data user yang sedang login
    Route::get("/users/current", [App\Http\Controllers\UserController::class, "get"]);
    // Route untuk mengupdate data user yang sedang login
    Route::patch("/users/current", [App\Http\Controllers\UserController::class, "update"]);
    // Route untuk logout user yang sedang login
    Route::delete("/users/logout", [App\Http\Controllers\UserController::class, "logout"]);
});