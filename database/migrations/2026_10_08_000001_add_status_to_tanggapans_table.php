<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menyimpan status laporan yang ditetapkan pada saat tanggapan diberikan,
     * sehingga riwayat penanganan (timeline) dapat ditampilkan.
     */
    public function up(): void
    {
        Schema::table('tanggapans', function (Blueprint $table) {
            $table->enum('status', ['menunggu', 'diproses', 'selesai', 'ditolak'])
                ->nullable()
                ->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('tanggapans', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
