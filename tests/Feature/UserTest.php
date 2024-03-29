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

    // Metode untuk menguji update data password user saat berhasil
    public function testUpdatePasswordSuccess()
    {
        // Menambahkan user baru dengan username "test"
        $this->seed([UserSeeder::class]);
        // Mengambil data user sebelum diupdate
        $oldUser = User::where("username", "test")->first();

        // Mengirimkan request PATCH untuk mengupdate password user dengan Authorization token
        $this->patch(
            "api/users/current",
            [
                "password" => "baru"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200) // Memastikan respons memiliki status code 200 (OK)
            // Memastikan respons berupa JSON dengan data yang sesuai
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "test"
                ]
            ]);

        // Mengambil data user setelah diupdate
        $newUser = User::where("username", "test")->first();
        // Memastikan password user telah diupdate
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    // Metode untuk menguji update data name user saat berhasil
    public function testUpdateNameSuccess()
    {
        // Menambahkan user baru dengan username "test"
        $this->seed([UserSeeder::class]);
        // Mengambil data user sebelum diupdate
        $oldUser = User::where("username", "test")->first();

        // Mengirimkan request PATCH untuk mengupdate name user dengan Authorization token
        $this->patch(
            "api/users/current",
            [
                "name" => "Jhon"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(200) // Memastikan respons memiliki status code 200 (OK)
            // Memastikan respons berupa JSON dengan data yang sesuai
            ->assertJson([
                "data" => [
                    "username" => "test",
                    "name" => "Jhon"
                ]
            ]);

        // Mengambil data user setelah diupdate
        $newUser = User::where("username", "test")->first();
        // Memastikan name user telah diupdate
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    // Metode untuk menguji update data user gagal
    public function testUpdateFailed()
    {
        // Menambahkan user baru dengan username "test"
        $this->seed([UserSeeder::class]);

        // Mengirimkan request PATCH dengan name yang lebih dari 100 karakter dan Authorization token
        $this->patch(
            "api/users/current",
            [
                "name" => "JhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhonJhon"
            ],
            [
                "Authorization" => "test"
            ]
        )->assertStatus(400) // Memastikan respons memiliki status code 400 (Bad Request)
            // Memastikan respons berupa JSON dengan pesan error yang sesuai
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field must not be greater than 100 characters."
                    ]
                ]
            ]);
    }

    // Metode untuk menguji logout user berhasil
    public function testLogoutSuccess()
    {
        // Menambahkan user baru dengan username "test"
        $this->seed([UserSeeder::class]);

        // Mengirimkan request DELETE ke endpoint /api/users/logout dengan Authorization token 'test'
        $this->delete(uri: "/api/users/logout", headers: [
            "Authorization" => "test"
        ])->assertStatus(200) // Memastikan respons memiliki status code 200 (OK)
            // Memastikan respons berupa JSON dengan data true
            ->assertJson([
                "data" => true
            ]);

        // Memeriksa apakah token user telah dihapus setelah logout
        $user = User::where("username", "test")->first();
        self::assertNull($user->token);
    }

    // Metode untuk menguji logout user gagal karena token tidak valid
    public function testLogoutFailed()
    {
        // Menambahkan user baru dengan username "test"
        $this->seed([UserSeeder::class]);

        // Mengirimkan request DELETE ke endpoint /api/users/logout dengan Authorization token 'salah'
        $this->delete(uri: "/api/users/logout", headers: [
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
