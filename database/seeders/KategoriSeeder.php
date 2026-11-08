<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategoris')->insert([
            ['nama_kategori' => 'Jalan Rusak & Berlubang', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Penanganan Sampah & Lingkungan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Penerangan Jalan Umum (PJU)', 'created_at' => now(), 'updated_at' => now()],
            ['nama_kategori' => 'Saluran Air & Drainase', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}