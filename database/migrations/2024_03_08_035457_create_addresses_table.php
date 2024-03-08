<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Membuat migration untuk tabel 'addresses'
return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id(); // Primary key bertipe big integer auto-increment
            $table->string("street", 200)->nullable(); // Kolom 'street' bertipe string dengan panjang maksimum 200 karakter, bisa kosong
            $table->string("city", 100)->nullable(); // Kolom 'city' bertipe string dengan panjang maksimum 100 karakter, bisa kosong
            $table->string("provience", 100)->nullable(); // Kolom 'province' bertipe string dengan panjang maksimum 100 karakter, bisa kosong
            $table->string("country", 100)->nullable(false); // Kolom 'country' bertipe string dengan panjang maksimum 100 karakter, tidak boleh kosong
            $table->string("postal_code", 10)->nullable(); // Kolom 'postal_code' bertipe string dengan panjang maksimum 10 karakter, bisa kosong
            $table->unsignedBigInteger("contact_id")->nullable(false); // Kolom 'contact_id' bertipe unsigned big integer, tidak boleh kosong
            $table->timestamps(); // Kolom 'created_at' dan 'updated_at' untuk track waktu pembuatan dan pembaruan

            // Menambahkan foreign key constraint untuk 'contact_id' yang merujuk ke 'id' pada tabel 'contacts'
            $table->foreign("contact_id")->on("contacts")->references("id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Menghapus tabel 'addresses' jika rollback migrasi
        Schema::dropIfExists('addresses');
    }
};
