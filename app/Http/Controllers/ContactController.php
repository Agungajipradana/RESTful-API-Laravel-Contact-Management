<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCreateRequest;
use App\Http\Requests\ContactUpdateRequest;
use App\Http\Resources\ContactCollection;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Controller untuk operasi CRUD pada kontak
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

    // Method untuk mengupdate kontak berdasarkan ID
    public function update(int $id, ContactUpdateRequest $request): ContactResource
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

        // Validasi data dari request
        $data = $request->validated();
        // Update data kontak
        $contact->fill($data);
        $contact->save();

        // Kembalikan response JSON dengan data kontak yang telah diupdate
        return new ContactResource($contact);
    }

    // Method untuk menghapus kontak berdasarkan ID
    public function delete(int $id): JsonResponse
    {
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

        // Hapus kontak
        $contact->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    // Method untuk mencari kontak berdasarkan nama, email, atau nomor telepon
    public function search(Request $request): ContactCollection
    {
        // Ambil user yang sedang terautentikasi
        $user = Auth::user();

        // Ambil halaman dan ukuran halaman dari request
        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        // Query untuk mencari kontak berdasarkan user_id
        $contacts = Contact::query()->where("user_id", $user->id);

        // Filter kontak berdasarkan nama, email, atau nomor telepon jika ada
        $contacts = $contacts->where(function (Builder $builder) use ($request) {
            $name = $request->input("name");
            if ($name) {
                $builder->where(function (Builder $builder) use ($name) {
                    $builder->orWhere("first_name", "like", "%" . $name . "%");
                    $builder->orWhere("last_name", "like", "%" . $name . "%");
                });
            }

            $email = $request->input("email");
            if ($email) {
                $builder->where("email", "like", "%" . $email . "%");
            }

            $phone = $request->input("phone");
            if ($phone) {
                $builder->where("phone", "like", "%" . $phone . "%");
            }
        });

        // Pagination
        $contacts = $contacts->paginate(perPage: $size, page: $page);

        // Mengembalikan hasil pencarian dalam bentuk collection
        return new ContactCollection($contacts);
    }
}
