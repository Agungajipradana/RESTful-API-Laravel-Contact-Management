<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    // Test untuk mengecek berhasil membuat kontak baru
    public function testCreateSuccess()
    {
        // Menjalankan seeder untuk menambahkan user dummy
        $this->seed([UserSeeder::class]);

        // Melakukan request POST untuk membuat kontak baru dengan data valid
        $this->post("/api/contacts", [
            "first_name" => "Jhon",
            "last_name" => "Doe",
            "email" => "jhon@gmail.com",
            "phone" => "083412354398"
        ], [
            "Authorization" => "test"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "first_name" => "Jhon",
                    "last_name" => "Doe",
                    "email" => "jhon@gmail.com",
                    "phone" => "083412354398"
                ]
            ]);
    }

    // Test untuk mengecek gagal membuat kontak baru karena data tidak valid
    public function testCreateFailed()
    {
        // Menjalankan seeder untuk menambahkan user dummy
        $this->seed([UserSeeder::class]);

        // Melakukan request POST untuk membuat kontak baru dengan data tidak valid
        $this->post("/api/contacts", [
            "first_name" => "",
            "last_name" => "Doe",
            "email" => "jhon",
            "phone" => "083412354398"
        ], [
            "Authorization" => "test"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ],

                    "email" => [
                        "The email field must be a valid email address."
                    ],
                ]
            ]);
    }

    // Test untuk mengecek gagal membuat kontak baru karena unauthorized
    public function testCreateUnauthorized()
    {
        // Menjalankan seeder untuk menambahkan user dummy
        $this->seed([UserSeeder::class]);

        // Melakukan request POST untuk membuat kontak baru dengan unauthorized
        $this->post("/api/contacts", [
            "first_name" => "",
            "last_name" => "Doe",
            "email" => "jhon",
            "phone" => "083412354398"
        ], [
            "Authorization" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    // Test untuk mengecek berhasil mendapatkan kontak
    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . $contact->id, [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "test",
                    "last_name" => "test",
                    "email" => "test@gmail.com",
                    "phone" => "111111",
                ]
            ]);
    }

    // Test untuk mengecek gagal mendapatkan kontak karena tidak ditemukan
    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . ($contact->id + 1), [
            "Authorization" => "test"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }

    // Test untuk mengecek gagal mendapatkan kontak karena bukan milik user tersebut
    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get("/api/contacts/" . $contact->id, [
            "Authorization" => "test2"
        ])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "not found"
                    ]
                ]
            ]);
    }
}
