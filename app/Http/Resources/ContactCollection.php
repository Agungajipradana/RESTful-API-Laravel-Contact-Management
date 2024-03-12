<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

// Kelas untuk mengelola koleksi kontak
class ContactCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Mengembalikan array yang berisi koleksi sumber daya kontak
        return [
            "data" => ContactResource::collection($this->collection)
        ];
    }
}
