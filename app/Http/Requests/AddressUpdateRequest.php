<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Memastikan bahwa user yang membuat request tidak null
        return $this->user() != null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "street" => ["nullable", "max:200"], // Alamat jalan maksimal 200 karakter
            "city" => ["nullable", "max:100"], // Kota maksimal 100 karakter
            "provience" => ["nullable", "max:100"], // Provinsi maksimal 100 karakter
            "country" => ["required", "max:100"], // Negara wajib diisi maksimal 100 karakter
            "postal_code" => ["nullable", "max:10"] // Kode pos maksimal 10 karakter
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        // Melempar HttpResponseException dengan response JSON yang berisi pesan kesalahan validasi
        throw new HttpResponseException(response([
            "errors" => $validator->getMessageBag()
        ], 400));
    }
}
