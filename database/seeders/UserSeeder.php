<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// Seeder class untuk menambahkan data user awal ke database
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Membuat user baru dengan username, password, dan name 'test'
        User::create([
            "username" => "test",
            "password" => Hash::make("test"),
            "name" => "test",
            "token" => "test"
        ]);

        // Membuat user baru dengan username, password, dan name 'test2'
        User::create([
            "username" => "test2",
            "password" => Hash::make("test2"),
            "name" => "test2",
            "token" => "test2"
        ]);
    }
}
