<?php

namespace Tests\Feature;

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
}
