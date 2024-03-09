<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model User yang merepresentasikan tabel 'users'
class User extends Model implements Authenticatable
{
    // Nama tabel yang akan digunakan oleh model
    protected $table = "users";
    // Nama primary key
    protected $primaryKey = "id";
    // Tipe data primary key
    protected $keyType = "int";
    // Mengaktifkan timestamps (created_at dan updated_at)
    public $timestamps = true;
    // Mengaktifkan incrementing untuk primary key
    public $incrementing = true;

    // Kolom yang dapat diisi secara massal (mass assignable) atau bisa diubah
    protected $fillable = [
        "username",
        "password",
        "name"
    ];

    // Relationship One-to-Many dengan model Contact
    public function contacts(): HasMany
    {
        // Mengembalikan relasi HasMany dengan model Contact
        // Kolom 'user_id' pada tabel 'contacts' akan merujuk ke kolom 'id' pada tabel 'users'
        return $this->hasMany(Contact::class, "user_id", "id");
    }

    // Metode untuk mengambil nama kolom sebagai identifier untuk autentikasi
    public function getAuthIdentifierName()
    {
        return "username";
    }

    // Metode untuk mengambil nilai identifier untuk autentikasi
    public function getAuthIdentifier()
    {
        return $this->username;
    }

    // Metode untuk mengambil password untuk autentikasi
    public function getAuthPassword()
    {
        return $this->password;
    }

    // Metode untuk mengambil remember token untuk autentikasi
    public function getRememberToken()
    {
        return $this->token;
    }

    // Metode untuk mengatur remember token untuk autentikasi
    public function setRememberToken($value)
    {
        $this->token = $value;
    }

    // Metode untuk mengambil nama kolom remember token untuk autentikasi
    public function getRememberTokenName()
    {
        return "token";
    }
}
