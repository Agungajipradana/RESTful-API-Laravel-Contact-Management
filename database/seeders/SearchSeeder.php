<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

// Seeder untuk menambahkan data kontak untuk user "test"
class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user dengan username "test"
        $user = User::where("username", "test")->first();

        // Loop untuk menambahkan 20 data kontak untuk user "test"
        for ($i = 0; $i < 20; $i++) {
            Contact::create([
                "first_name" => "first" . $i,
                "last_name" => "last" . $i,
                "email" => "test" . $i . "@gmail.com",
                "phone" => "11111" . $i,
                "user_id" => $user->id
            ]);
        }
    }
}
