<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('laporans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID Warga pelapor
        $table->foreignId('kategori_id')->constrained()->onDelete('cascade'); // ID Kategori
        $table->string('judul');
        $table->text('deskripsi');
        $table->decimal('lat', 10, 8)->nullable(); // Koordinat GPS Latitude
        $table->decimal('lng', 11, 8)->nullable(); // Koordinat GPS Longitude
        $table->string('foto_bukti'); // Path/nama file foto
        $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])->default('menunggu');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporans');
    }
};
