<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


// Request class untuk validasi inputan saat user melakukan registrasi
class UserRegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Mengembalikan nilai true karena tidak ada aturan khusus untuk otorisasi
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Aturan validasi untuk inputan username, password, dan name
        return [
            "username" => ["required", "max:100"],
            "password" => ["required", "max:100"],
            "name" => ["required", "max:100"],
        ];
    }

    // Fungsi untuk menangani kesalahan validasi
    protected function failedValidation(Validator $validator)
    {
        // Melempar HttpResponseException dengan response JSON yang berisi pesan kesalahan validasi
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
