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
        Schema::create('tabung_activity', function (Blueprint $table) {
            $table->id();
            $table->string('activity'); // Terima tabung dari armada yang di scan qr
            $table->string('nama_user'); // diambil dari auth login
            $table->json('qr_tabung'); // ada multi tabung (stored as JSON array)
            $table->string('lokasi_gudang'); // dari qr
            $table->string('armada'); // dari qr
            $table->text('keterangan')->nullable(); // optional
            $table->enum('status', ['Kosong', 'Isi'])->default('Isi'); // Status tabung
            $table->unsignedBigInteger('user_id'); // Foreign key ke users table
            $table->string('transaksi_id')->unique(); // ID transaksi unik
            $table->date('tanggal_aktivitas'); // Tanggal aktivitas
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index untuk performa
            $table->index(['user_id', 'tanggal_aktivitas']);
            $table->index('transaksi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tabung_activity');
    }
};
