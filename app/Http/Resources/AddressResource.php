<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Kelas untuk mengubah model Address menjadi array JSON
class AddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id, // Mengambil ID dari model Address
            "street" => $this->street, // Mengambil nama jalan dari model Address
            "city" => $this->city,  // Mengambil nama kota dari model Address
            "provience" => $this->provience, // Mengambil nama provinsi dari model Address
            "country" => $this->country, // Mengambil nama negara dari model Address
            "postal_code" => $this->postal_code // Mengambil kode pos dari model Address
        ];
    }
}
