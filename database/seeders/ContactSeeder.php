<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Mengambil user dengan username "test"
        $user = User::where("username", "test")->first();

        // Membuat data kontak baru untuk user dengan username "test"
        Contact::create([
            "first_name" => "test",
            "last_name" => "test",
            "email" => "test@gmail.com",
            "phone" => "111111",
            "user_id" => $user->id
        ]);
    }
}
