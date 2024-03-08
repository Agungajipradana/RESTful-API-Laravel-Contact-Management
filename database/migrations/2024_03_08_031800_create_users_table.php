<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Membuat tabel 'users' dengan beberapa kolom
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Kolom id yang otomatis bertambah
            $table->string("username", 100)->nullable(false)->unique("users_username_unique"); // Kolom username, tidak boleh kosong dan harus unik
            $table->string("password", 100)->nullable(false); // Kolom password, tidak boleh kosong
            $table->string("name", 100)->nullable(false); // Kolom name, tidak boleh kosong
            $table->string("token", 100)->nullable()->unique("users_token_unique"); // Kolom token, boleh kosong dan harus unik
            $table->timestamps(); // Kolom created_at dan updated_at untuk mengikuti waktu pembuatan dan pembaruan record
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus tabel 'users'
        Schema::dropIfExists('users');
    }
};
