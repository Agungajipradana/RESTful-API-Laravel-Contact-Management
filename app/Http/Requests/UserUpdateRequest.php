<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

// Form request untuk validasi update data user
class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Memeriksa apakah user terautentikasi (tidak null)
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        // Aturan validasi untuk name (opsional, maksimal 100 karakter) dan password (opsional, maksimal 100 karakter)
        return [
            "name" => ["nullable", "max:100"],
            "password" => ["nullable", "max:100"]
        ];
    }

    // Metode yang akan dipanggil jika validasi gagal
    protected function failedValidation(Validator $validator)
    {
        // Melempar HttpResponseException dengan response JSON yang berisi pesan kesalahan validasi
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
