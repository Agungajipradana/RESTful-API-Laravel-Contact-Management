<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// Kelas untuk menguji fitur terkait user
class UserTest extends TestCase
{
    // Metode untuk menguji registrasi user berhasil
    public function testRegisterSuccess()
    {
        // Mengirimkan permintaan POST ke endpoint /api/users dengan data user baru
        $this->post("/api/users", [
            "username" => "jhon",
            "password" => "rahasia",
            "name" => "Jhon Doe"
        ])->assertStatus(201) // Memastikan respons memiliki status code 201 (Created)
            // Memastikan respons berupa JSON dengan data yang sesuai
            ->assertJson([
                "data" => [
                    "username" => "jhon",
                    "name" => "Jhon Doe"
                ]
            ]);
    }

    // Metode untuk menguji registrasi user gagal karena data yang tidak lengkap
    public function testRegisterFailed()
    {
        // Mengirimkan permintaan POST ke endpoint /api/users dengan data user yang tidak lengkap
        $this->post("/api/users", [
            "username" => "",
            "password" => "",
            "name" => ""
        ])->assertStatus(400) // Memastikan respons memiliki status code 400 (Bad Request)
            // Memastikan respons berupa JSON dengan pesan error yang sesuai
            ->assertJson([
                "errors" => [
                    "username" => [
                        "The username field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ]
                ]
            ]);
    }

    // Metode untuk menguji registrasi user gagal karena username sudah terdaftar
    public function testRegisterUsernameAlreadyExists()
    {
        // Melakukan registrasi user berhasil untuk membuat username "jhon" sudah terdaftar
        $this->testRegisterSuccess();
        // Mengirimkan permintaan POST ke endpoint /api/users dengan data user yang memiliki username yang sama
        $this->post("/api/users", [
            "username" => "jhon",
            "password" => "rahasia",
            "name" => "Jhon Doe"
        ])->assertStatus(400) // Memastikan respons memiliki status code 400 (Bad Request)
            // Memastikan respons berupa JSON dengan pesan error yang sesuai
            ->assertJson([
                "errors" => [
                    "username" => [
                        "username already registered"
                    ]
                ]
            ]);
    }

    // Metode untuk menguji login user berhasil
    public function testLoginSuccess()
    {
        // Menjalankan seeder UserSeeder untuk menambahkan data user baru ke database
        $this->seed([UserSeeder::class]);
        // Mengirimkan permintaan POST ke endpoint /api/users/login dengan data login user yang benar
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test"
        ])->assertStatus(200) // Memastikan respons memiliki status code 200 (OK)
            // Memastikan respons berupa JSON dengan data user yang sesuai
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);

        // Memeriksa apakah user memiliki token yang tidak null setelah login
        $user = User::where("username", "test")->first();
        self::assertNotNull($user->token);
    }

    // Metode untuk menguji login user gagal karena username tidak ditemukan
    public function testLoginFailedUsernameNotFound()
    {
        // Mengirimkan permintaan POST ke endpoint /api/users/login dengan username yang tidak terdaftar
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "test"
        ])->assertStatus(401) // Memastikan respons memiliki status code 401 (Unauthorized)
            // Memastikan respons berupa JSON dengan pesan error yang sesuai
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    // Metode untuk menguji login user gagal karena password salah
    public function testLoginFailedPasswordWrong()
    {
        // Menjalankan seeder UserSeeder untuk menambahkan data user baru ke database
        $this->seed([UserSeeder::class]);
        // Mengirimkan permintaan POST ke endpoint /api/users/login dengan password yang salah
        $this->post("/api/users/login", [
            "username" => "test",
            "password" => "salah"
        ])->assertStatus(401) // Memastikan respons memiliki status code 401 (Unauthorized)
            // Memastikan respons berupa JSON dengan pesan error yang sesuai
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password wrong"
                    ]
                ]
            ]);
    }

    // Metode untuk menguji mendapatkan data user saat berhasil
    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class]);

        // Mengirimkan permintaan GET ke endpoint /api/users/current dengan token 'test'
        $this->get("api/users/current", [
            "Authorization" => "test"
        ])->assertStatus(200) // Memastikan respons memiliki status code 200 (OK)
            // Memastikan respons berupa JSON dengan data user yang sesuai
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);
    }

    // Metode untuk menguji mendapatkan data user saat tidak terautentikasi
    public function testGetUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        // Mengirimkan permintaan GET ke endpoint /api/users/current tanpa token
        $this->get("api/users/current")
            ->assertStatus(401) // Memastikan respons memiliki status code 401 (Unauthorized)
            // Memastikan respons berupa JSON dengan pesan error yang sesuai
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }

    // Metode untuk menguji mendapatkan data user saat token tidak valid
    public function testGetInvalidToken()
    {
        $this->seed([UserSeeder::class]);

        // Mengirimkan permintaan GET ke endpoint /api/users/current dengan token 'salah'
        $this->get("api/users/current", [
            "Authorization" => "salah"
        ])->assertStatus(401) // Memastikan respons memiliki status code 401 (Unauthorized)
            // Memastikan respons berupa JSON dengan pesan error yang sesuai
            ->assertJson([
                "errors" => [
                    "message" => [
                        "unauthorized"
                    ]
                ]
            ]);
    }
}
