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
    Schema::create('tanggapans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('laporan_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID Petugas/Admin yang menanggapi
        $table->text('catatan');
        $table->string('foto_hasil')->nullable(); // Foto bukti pengerjaan/perbaikan
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanggapans');
    }
};
