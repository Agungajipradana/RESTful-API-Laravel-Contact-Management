<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
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

    // Test untuk mengecek berhasil mengupdate kontak
    public function testUpdateSuccess()
    {
        // Menjalankan seeder untuk menambahkan user dan kontak dummy
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        // Mengambil satu data kontak dari database
        $contact = Contact::query()->limit(1)->first();

        // Melakukan request PUT untuk mengupdate kontak dengan data baru
        $this->put("/api/contacts/" . $contact->id, [
            "first_name" => "test2",
            "last_name" => "test2",
            "email" => "test2@gmail.com",
            "phone" => "1111112",
        ], [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "first_name" => "test2",
                    "last_name" => "test2",
                    "email" => "test2@gmail.com",
                    "phone" => "1111112",
                ]
            ]);
    }

    // Test untuk mengecek gagal mengupdate kontak karena validasi error
    public function testUpdateValidationError()
    {
        // Menjalankan seeder untuk menambahkan user dan kontak dummy
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        // Mengambil satu data kontak dari database
        $contact = Contact::query()->limit(1)->first();

        // Melakukan request PUT untuk mengupdate kontak dengan data tidak valid
        $this->put("/api/contacts/" . $contact->id, [
            "first_name" => "",
            "last_name" => "test2",
            "email" => "test2@gmail.com",
            "phone" => "1111112",
        ], [
            "Authorization" => "test"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "first_name" => [
                        "The first name field is required."
                    ]
                ]
            ]);
    }

    // Test untuk menghapus kontak dengan sukses
    public function testDeleteSuccess()
    {
        // Menjalankan seeder untuk menambahkan user dummy dan kontak dummy
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        // Mengambil salah satu kontak
        $contact = Contact::query()->limit(1)->first();

        // Mengirimkan request DELETE untuk menghapus kontak
        $this->delete("/api/contacts/" . $contact->id, [], [
            "Authorization" => "test"
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    // Test untuk menghapus kontak yang tidak ditemukan
    public function testDeleteNotFound()
    {
        // Menjalankan seeder untuk menambahkan user dummy dan kontak dummy
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        // Mengambil salah satu kontak
        $contact = Contact::query()->limit(1)->first();

        // Mengirimkan request DELETE untuk menghapus kontak yang tidak ada
        $this->delete("/api/contacts/" . ($contact->id + 1), [], [
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

    // Test untuk mencari kontak berdasarkan nama depan
    public function testSearchByFirstName()
    {
        // Menjalankan seeder untuk menambahkan data dummy
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        // Melakukan request GET untuk mencari kontak berdasarkan nama depan
        $response = $this->get("/api/contacts?name=first", [
            "Authorization" => "test"
        ])
            ->assertStatus(200) // Memastikan response status code 200 (OK)
            ->json(); // Mengubah response menjadi array JSON

        // Mencetak response dengan format JSON yang mudah dibaca
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        // Memastikan jumlah data yang dikembalikan sesuai dengan yang diharapkan
        self::assertEquals(10, count($response["data"]));
        // Memastikan total data yang ada sesuai dengan yang diharapkan
        self::assertEquals(20, $response["meta"]["total"]);
    }

    // Test untuk mencari kontak berdasarkan nama belakang
    public function testSearchByLastName()
    {
        // Menjalankan seeder untuk menambahkan data dummy
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        // Melakukan request GET untuk mencari kontak berdasarkan nama belakang
        $response = $this->get("/api/contacts?name=last", [
            "Authorization" => "test"
        ])
            ->assertStatus(200) // Memastikan response status code 200 (OK)
            ->json(); // Mengubah response menjadi array JSON

        // Mencetak response dengan format JSON yang mudah dibaca
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        // Memastikan jumlah data yang dikembalikan sesuai dengan yang diharapkan
        self::assertEquals(10, count($response["data"]));
        // Memastikan total data yang ada sesuai dengan yang diharapkan
        self::assertEquals(20, $response["meta"]["total"]);
    }

    // Test untuk mencari kontak berdasarkan email
    public function testSearchByEmail()
    {
        // Menjalankan seeder untuk menambahkan data dummy
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        // Melakukan request GET untuk mencari kontak berdasarkan email
        $response = $this->get("/api/contacts?email=test", [
            "Authorization" => "test"
        ])
            ->assertStatus(200) // Memastikan response status code 200 (OK)
            ->json(); // Mengubah response menjadi array JSON

        // Mencetak response dengan format JSON yang mudah dibaca
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        // Memastikan jumlah data yang dikembalikan sesuai dengan yang diharapkan
        self::assertEquals(10, count($response["data"]));
        // Memastikan total data yang ada sesuai dengan yang diharapkan
        self::assertEquals(20, $response["meta"]["total"]);
    }

    // Test untuk mencari kontak berdasarkan nomor telepon
    public function testSearchByPhone()
    {
        // Menjalankan seeder untuk menambahkan data dummy
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        // Melakukan request GET untuk mencari kontak berdasarkan nomor telepon
        $response = $this->get("/api/contacts?phone=11111", [
            "Authorization" => "test"
        ])
            ->assertStatus(200) // Memastikan response status code 200 (OK)
            ->json(); // Mengubah response menjadi array JSON

        // Mencetak response dengan format JSON yang mudah dibaca
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        // Memastikan jumlah data yang dikembalikan sesuai dengan yang diharapkan
        self::assertEquals(10, count($response["data"]));
        // Memastikan total data yang ada sesuai dengan yang diharapkan
        self::assertEquals(20, $response["meta"]["total"]);
    }

    // Test untuk mencari kontak yang tidak ditemukan
    public function testSearchNotFound()
    {
        // Menjalankan seeder untuk menambahkan data dummy
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        // Melakukan request GET untuk mencari kontak yang tidak ada
        $response = $this->get("/api/contacts?name=tidakada", [
            "Authorization" => "test"
        ])
            ->assertStatus(200) // Memastikan response status code 200 (OK)
            ->json(); // Mengubah response menjadi array JSON

        // Mencetak response dengan format JSON yang mudah dibaca
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        // Memastikan tidak ada data yang dikembalikan
        self::assertEquals(0, count($response["data"]));
        // Memastikan total data yang ada sesuai dengan yang diharapkan
        self::assertEquals(0, $response["meta"]["total"]);
    }

    // Test untuk mencari kontak dengan pagination
    public function testSearchWithPage()
    {
        // Menjalankan seeder untuk menambahkan data dummy
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        // Melakukan request GET untuk mencari kontak dengan pagination
        $response = $this->get("/api/contacts?size=5&page=2", [
            "Authorization" => "test"
        ])
            ->assertStatus(200) // Memastikan response status code 200 (OK)
            ->json(); // Mengubah response menjadi array JSON

        // Mencetak response dengan format JSON yang mudah dibaca
        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        // Memastikan jumlah data yang dikembalikan sesuai dengan yang diharapkan
        self::assertEquals(5, count($response["data"]));
        // Memastikan total data yang ada sesuai dengan yang diharapkan
        self::assertEquals(20, $response["meta"]["total"]);
        // Memastikan halaman saat ini sesuai dengan yang diharapkan
        self::assertEquals(2, $response["meta"]["current_page"]);
    }
}
