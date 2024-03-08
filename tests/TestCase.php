<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

// Kelas abstract TestCase yang digunakan untuk testing
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // Method setUp() yang akan dijalankan sebelum setiap test
    protected function setUp(): void
    {
        parent::setUp();

        // Menghapus semua data dari tabel 'addresses', 'contacts', dan 'users' sebelum setiap test
        DB::delete("delete from addresses");
        DB::delete("delete from contacts");
        DB::delete("delete from users");
    }
}
