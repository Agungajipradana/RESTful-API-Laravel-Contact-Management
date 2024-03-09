<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Resource class untuk mengubah model User menjadi format JSON
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Mengembalikan array yang berisi data user
        return [
            "id" => $this->id,
            "username" => $this->username,
            "name" => $this->name,
            "token" => $this->whenNotNull($this->token)
        ];
    }
}
