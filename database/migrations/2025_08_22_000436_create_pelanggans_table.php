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
        Schema::create('pelanggans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pelanggan')->unique();
            $table->string('nama_pelanggan');
            $table->text('lokasi_pelanggan');
            $table->decimal('harga_tabung', 10, 2);
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('jenis_pelanggan', ['umum', 'agen'])->default('umum');
            $table->string('penanggung_jawab')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pelanggans');
    }
};
