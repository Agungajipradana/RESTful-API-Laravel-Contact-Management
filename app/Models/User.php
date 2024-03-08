<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
