<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Method untuk membuat kontak baru
    public function create(ContactCreateRequest $request): JsonResponse
    {
        // Validasi data dari request
        $data = $request->validated();
        // Ambil user yang sedang terautentikasi
        $user = Auth::user();

        // Buat instance Contact baru dengan data yang diberikan
        $contact = new Contact($data);
        // Set user_id dari kontak ke id user yang sedang terautentikasi
        $contact->user_id = $user->id;
        // Simpan kontak ke database
        $contact->save();

        // Kembalikan respons JSON dengan kontak yang telah dibuat dan status code 201 (created)
        return(new ContactResource($contact))->response()->setStatusCode(201);
    }

    // Method untuk mendapatkan kontak berdasarkan ID
    public function get(int $id): ContactResource
    {
        // Ambil user yang sedang terautentikasi
        $user = Auth::user();

        // Cari kontak berdasarkan ID dan user_id
        $contact = Contact::where("id", $id)->where("user_id", $user->id)->first();

        // Jika kontak tidak ditemukan, kirim response JSON dengan status code 404 (not found)
        if (!$contact) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        // Jika kontak ditemukan, kembalikan response JSON dengan data kontak
        return new ContactResource($contact);
    }
}
