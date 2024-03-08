<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migration untuk membuat tabel 'contacts'
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Membuat tabel 'contacts' dengan beberapa kolom
        Schema::create('contacts', function (Blueprint $table) {
            $table->id(); // Kolom id, sebagai primary key
            $table->string("first_name", 100)->nullable(false); // Kolom first_name, tidak boleh kosong
            $table->string("last_name", 100)->nullable(); // Kolom last_name, boleh kosong
            $table->string("email", 200)->nullable(); // Kolom email, boleh kosong
            $table->string("phone", 20)->nullable(); // Kolom phone, boleh kosong
            $table->unsignedBigInteger("user_id")->nullable(false); // Kolom user_id, tidak boleh kosong
            $table->timestamps(); // Kolom timestamps, otomatis mengisi created_at dan updated_at

            // Menambahkan foreign key constraint untuk kolom user_id
            $table->foreign("user_id")->on("users")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus tabel 'contacts'
        Schema::dropIfExists('contacts');
    }
};
