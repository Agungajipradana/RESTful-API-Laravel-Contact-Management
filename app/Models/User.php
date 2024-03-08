<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Model User yang merepresentasikan tabel 'users'
class User extends Model
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
}
