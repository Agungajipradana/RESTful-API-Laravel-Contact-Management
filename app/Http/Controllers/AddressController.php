<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressCreateRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Models\Contact;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controller untuk menangani operasi terkait alamat
class AddressController extends Controller
{
    // Method untuk mendapatkan kontak berdasarkan ID, jika tidak ditemukan akan melempar HttpResponseException
    private function getContact(User $user, int $idContact): Contact
    {
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

        return $contact;
    }

    // Method untuk mendapatkan alamat berdasarkan ID kontak dan ID alamat, jika tidak ditemukan akan melempar HttpResponseException
    private function getAddress(Contact $contact, int $idAddress): Address
    {
        $address = Address::where("contact_id", $contact->id)->where("id", $idAddress)->first();

        if (!$address) {
            // Jika alamat tidak ditemukan, melempar HttpResponseException dengan response JSON
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        return $address;
    }

    // Method untuk membuat alamat baru untuk kontak tertentu
    public function create(int $idContact, AddressCreateRequest $request): JsonResponse
    {
        // Mendapatkan informasi user yang sedang login
        $user = Auth::user();
        // Mengambil kontak berdasarkan ID
        $contact = $this->getContact($user, $idContact);

        $data = $request->validated(); // Validasi data alamat
        $address = new Address($data); // Membuat instance Address baru
        $address->contact_id = $contact->id; // Mengaitkan alamat dengan kontak
        $address->save();  // Menyimpan alamat ke database

        // Mengembalikan response JSON dengan status code 201
        return(new AddressResource($address))->response()->setStatusCode(201);
    }

    // Method untuk mendapatkan detail alamat berdasarkan ID kontak dan ID alamat
    public function get(int $idContact, int $idAddress): AddressResource
    {
        // Mendapatkan informasi user yang sedang login
        $user = Auth::user();
        // Mengambil kontak berdasarkan ID
        $contact = $this->getContact($user, $idContact);

        // Mengambil alamat berdasarkan ID kontak dan ID alamat
        $address = $this->getAddress($contact, $idAddress);

        // Mengembalikan response JSON dengan detail alamat
        return new AddressResource($address);
    }

    // Method untuk mengupdate alamat berdasarkan ID kontak dan ID alamat
    public function update(int $idContact, int $idAddress, AddressUpdateRequest $request): AddressResource
    {
        // Mendapatkan informasi user yang sedang login
        $user = Auth::user();
        // Mendapatkan informasi user yang sedang login
        $contact = $this->getContact($user, $idContact);
        // Mendapatkan alamat berdasarkan ID kontak dan ID alamat
        $address = $this->getAddress($contact, $idAddress);

        $data = $request->validated(); // Validasi data alamat
        $address->fill($data); // Mengisi data alamat dengan data baru
        $address->save(); // Menyimpan perubahan data alamat ke database

        // Mengembalikan response JSON dengan data alamat yang telah diupdate
        return new AddressResource($address);
    }

    // Method untuk menghapus alamat berdasarkan ID kontak dan ID alamat
    public function delete(int $idContact, int $idAddress): JsonResponse
    {

        // Mendapatkan informasi user yang sedang login
        $user = Auth::user();
        // Mendapatkan informasi user yang sedang login
        $contact = $this->getContact($user, $idContact);
        // Mendapatkan alamat berdasarkan ID kontak dan ID alamat
        $address = $this->getAddress($contact, $idAddress);

        // Menghapus alamat dari database
        $address->delete();

        // Mengembalikan response JSON dengan status code 200
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }
}
