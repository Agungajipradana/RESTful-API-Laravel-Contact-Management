<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// Model Contact yang merepresentasikan tabel 'contacts'
class Contact extends Model
{
    // Nama primary key
    protected $primaryKey = "id";
    // Tipe data primary key
    protected $keyType = "int";
    // Nama tabel yang akan digunakan oleh model
    protected $table = "contacts";
    // Mengaktifkan incrementing untuk primary key
    public $incrementing = true;
    // Mengaktifkan timestamps (created_at dan updated_at)
    public $timestamps = true;

    // Relationship Many-to-One dengan model User
    public function user(): BelongsTo
    {
        // Mengembalikan relasi BelongsTo dengan model User
        // Kolom 'user_id' pada tabel 'contacts' akan merujuk ke kolom 'id' pada tabel 'users'
        return $this->belongsTo(Contact::class, "user_id", "id");
    }
}
