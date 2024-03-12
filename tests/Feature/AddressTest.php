<?php

namespace Tests\Feature;

use App\Models\Contact;
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
}
