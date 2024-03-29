<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Contact;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    // Test untuk membuat alamat baru berhasil
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            "/api/contacts/" . $contact->id . "/addresses",
            [
                "street" => "test",
                "city" => "test",
                "provience" => "test",
                "country" => "test",
                "postal_code" => "213123"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(201)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "provience" => "test",
                    "country" => "test",
                    "postal_code" => "213123"
                ]
            ]);
    }

    // Test untuk membuat alamat baru gagal karena field country tidak diisi
    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            "/api/contacts/" . $contact->id . "/addresses",
            [
                "street" => "test",
                "city" => "test",
                "provience" => "test",
                "country" => "",
                "postal_code" => "213123"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => [
                        "The country field is required."
                    ]
                ]
            ]);
    }

    // Test untuk membuat alamat baru pada kontak yang tidak ditemukan
    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post(
            "/api/contacts/" . ($contact->id + 1) . "/addresses",
            [
                "street" => "test",
                "city" => "test",
                "provience" => "test",
                "country" => "test",
                "postal_code" => "213123"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    // Test untuk mendapatkan alamat dengan sukses
    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get(
            "/api/contacts/" . $address->contact_id . "/addresses/" . $address->id,
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "test",
                    "city" => "test",
                    "provience" => "test",
                    "country" => "test",
                    "postal_code" => "11111"
                ]
            ]);
    }

    // Test untuk mendapatkan alamat yang tidak ditemukan
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->get(
            "/api/contacts/" . $address->contact_id . "/addresses/" . ($address->id + 1),
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    // Tes untuk memperbarui alamat dengan berhasil
    public function testUpdateSuccess()
    {
        // Menyebarkan data dummy
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        // Melakukan request PUT untuk memperbarui alamat
        $this->put(
            "/api/contacts/" . $address->contact_id . "/addresses/" . $address->id,
            [
                "street" => "update",
                "city" => "update",
                "provience" => "update",
                "country" => "update",
                "postal_code" => "22222"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => [
                    "street" => "update",
                    "city" => "update",
                    "provience" => "update",
                    "country" => "update",
                    "postal_code" => "22222"
                ]
            ]);
    }

    // Tes untuk memperbarui alamat dengan kegagalan (field country tidak diisi)
    public function testUpdateFailed()
    {
        // Menyebarkan data dummy
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        // Melakukan request PUT untuk memperbarui alamat
        $this->put(
            "/api/contacts/" . $address->contact_id . "/addresses/" . $address->id,
            [
                "street" => "update",
                "city" => "update",
                "provience" => "update",
                "country" => "",
                "postal_code" => "22222"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "country" => [
                        "The country field is required."
                    ]
                ]
            ]);
    }

    // Tes untuk memperbarui alamat yang tidak ditemukan
    public function testUpdateNotFound()
    {
        // Menyebarkan data dummy
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        // Melakukan request PUT untuk memperbarui alamat
        $this->put(
            "/api/contacts/" . $address->contact_id . "/addresses/" . ($address->id + 1),
            [
                "street" => "update",
                "city" => "update",
                "provience" => "update",
                "country" => "update",
                "postal_code" => "22222"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    // Tes untuk menghapus alamat dengan berhasil
    public function testDeleteSuccess()
    {
        // Menyebarkan data dummy
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        // Melakukan request DELETE untuk menghapus alamat
        $this->delete(
            "/api/contacts/" . $address->contact_id . "/addresses/" . $address->id,
            [
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    // Tes untuk menghapus alamat yang tidak ditemukan
    public function testDeleteNotFound()
    {
        // Menyebarkan data dummy
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        // Melakukan request DELETE untuk menghapus alamat yang tidak ditemukan
        $this->delete(
            "/api/contacts/" . $address->contact_id . "/addresses/" . ($address->id + 1),
            [
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    // Tes untuk mendapatkan list alamat dengan berhasil
    public function testListSuccess()
    {
        // Menyebarkan data dummy
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        // Melakukan request GET untuk mendapatkan daftar alamat
        $this->get(
            "/api/contacts/" . $contact->id . "/addresses",
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200) // Memastikan respons memiliki status 200 (OK)
            // Memastikan respons berupa JSON dengan struktur yang diharapkan
            ->assertJson([
                "data" => [
                    [
                        "street" => "test",
                        "city" => "test",
                        "provience" => "test",
                        "country" => "test",
                        "postal_code" => "11111"
                    ]
                ]
            ]);
    }

    // Tes untuk mendapatkan list alamat yang tidak ditemukan
    public function testListContactNotFound()
    {
        // Menyiapkan data dummy untuk pengujian
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        // Melakukan request GET untuk mendapatkan daftar alamat dari kontak yang tidak ada
        $this->get(
            "/api/contacts/" . ($contact->id + 1) . "/addresses",
            [
                "Authorization" => "test"
            ]
        )->assertStatus(404) // Memastikan respons memiliki status 404 (Not Found)
            // Memastikan respons berupa JSON dengan struktur yang diharapkan
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }
}
