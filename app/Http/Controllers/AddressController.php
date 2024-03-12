<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controller untuk menangani operasi terkait alamat
class AddressController extends Controller
{
    // Method untuk membuat alamat baru untuk kontak tertentu
    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        // Mendapatkan informasi user yang sedang login
        $user = Auth::user();
        // Mengambil kontak berdasarkan ID
        $contact = Contact::where("user_id", $user->id)->where("id", $idContact)->first();

        if (!$contact) {
            // Jika kontak tidak ditemukan, melempar HttpResponseException dengan response JSON
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $data = $request->validated(); // Validasi data alamat
        $address = new Address($data); // Membuat instance Address baru
        $address->contact_id = $contact->id; // Mengaitkan alamat dengan kontak
        $address->save();  // Menyimpan alamat ke database

        // Mengembalikan response JSON dengan status code 201
        return(new AddressResource($address))->response()->setStatusCode(201);
    }
}
