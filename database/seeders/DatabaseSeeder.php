<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            KategoriSeeder::class,
        ]);

        // 2. Buat Akun Admin untuk Pengujian Login
        User::create([
            'name' => 'Admin Dinas',
            'email' => 'admin@laporkota.go.id',
            'password' => Hash::make('pompom123'),
            'role' => 'admin',
        ]);

        // 3. Buat Akun Warga untuk Pengujian Login
        User::create([
            'name' => 'Warga Test',
            'email' => 'warga@gmail.com',
            'password' => Hash::make('pompom123'),
            'role' => 'warga',
        ]);

        // 4. Buat Akun Eksekutif (pimpinan) untuk Pengujian Dashboard Monitoring
        User::create([
            'name' => 'Pimpinan Dinas',
            'email' => 'eksekutif@laporkota.go.id',
            'password' => Hash::make('pompom123'),
            'role' => 'eksekutif',
        ]);
    }
}