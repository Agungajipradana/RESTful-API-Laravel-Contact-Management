<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

// Request class untuk validasi data saat mengupdate kontak
class ContactUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Memeriksa apakah user terautentikasi
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Aturan validasi untuk setiap field yang akan diupdate
        return [
            "first_name" => ["required", "max:100"],
            "last_name" => ["nullable", "max:100"],
            "email" => ["nullable", "max:200", "email"],
            "phone" => ["nullable", "max:20"]
        ];
    }

    // Method yang akan dipanggil jika validasi gagal
    protected function failedValidation(Validator $validator)
    {
        // Melempar HttpResponseException dengan response JSON yang berisi pesan kesalahan validasi
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
