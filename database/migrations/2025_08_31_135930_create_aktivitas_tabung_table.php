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
        Schema::create('aktivitas_tabung', function (Blueprint $table) {
            $table->id();
            $table->string('nama_aktivitas')->default('Terima Tabung');
            $table->string('dari');
            $table->string('tujuan');
            $table->text('tabung'); // JSON field for tabung data
            $table->text('keterangan')->nullable();
            $table->string('nama_petugas');
            $table->integer('id_user')->default(0);
            $table->integer('total_tabung');
            $table->string('tanggal', 20);
            $table->string('status', 100)->default('Pending');
            $table->datetime('waktu')->nullable();
            
            // Add index for better performance
            $table->index(['id_user', 'tanggal']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aktivitas_tabung');
    }
};
