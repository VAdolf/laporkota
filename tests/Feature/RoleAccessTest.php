<?php

use App\Models\User;

test('role admin pada seeder-style create tersimpan (mass assignment)', function () {
    $admin = User::create([
        'name' => 'Admin',
        'email' => 'a@example.com',
        'password' => 'password',
        'role' => 'admin',
    ]);

    expect($admin->fresh()->role)->toBe('admin');
});

test('pengguna baru otomatis berperan warga', function () {
    $user = User::create(['name' => 'W', 'email' => 'w@example.com', 'password' => 'password']);

    expect($user->role)->toBe('warga');
    expect($user->fresh()->role)->toBe('warga');
});

test('warga tidak bisa mengakses halaman admin dan monitoring', function () {
    $warga = User::factory()->create(['role' => 'warga']);

    $this->actingAs($warga)->get('/monitoring')->assertForbidden();
    $this->actingAs($warga)->get('/kelola/laporan')->assertForbidden();
});

test('admin dan eksekutif bisa melihat monitoring dan daftar laporan', function () {
    foreach (['admin', 'eksekutif'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get('/monitoring')->assertOk();
        $this->actingAs($user)->get('/kelola/laporan')->assertOk();
    }
});

test('admin dan eksekutif tidak bisa membuat laporan (khusus warga)', function () {
    foreach (['admin', 'eksekutif'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get('/lapor')->assertForbidden();
    }
});

test('dashboard mengarahkan admin dan eksekutif ke monitoring', function () {
    foreach (['admin', 'eksekutif'] as $role) {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('monitoring.index'));
    }
});

test('tamu diarahkan ke halaman login', function () {
    $this->get('/monitoring')->assertRedirect('/login');
    $this->get('/lapor')->assertRedirect('/login');
});
