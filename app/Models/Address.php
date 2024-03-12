<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    // Nama tabel yang akan digunakan oleh model
    protected $table = "addresses";
    // Nama primary key
    protected $primaryKey = "id";
    // Tipe data primary key
    protected $keyType = "int";
    // Mengaktifkan incrementing untuk primary key
    public $incrementing = true;
    // Mengaktifkan timestamps (created_at dan updated_at)
    public $timestamps = true;

    // Kolom yang dapat diisi secara massal (mass assignable) atau bisa diubah
    protected $fillable = [
        "street",
        "city",
        "provience",
        "country",
        "postal_code"
    ];

    // Relationship Many-to-One dengan model Contact
    public function contact(): BelongsTo
    {
        // Mengembalikan relasi BelongsTo dengan model Contact
        // Kolom 'contact_id' pada tabel 'addresses' akan merujuk ke kolom 'id' pada tabel 'contacts'
        return $this->belongsTo(Contact::class, "contact_id", "id");
    }
}
