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
        Schema::create('laporan_pelanggan', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('kode_pelanggan');
            $table->string('keterangan');
            $table->json('list_tabung')->comment('JSON berisi array kode_tabung dan volume');
            $table->integer('tabung')->nullable();
            $table->decimal('harga', 15, 2)->nullable();
            $table->decimal('tambahan_deposit', 15, 2)->nullable();
            $table->decimal('pengurangan_deposit', 15, 2)->nullable();
            $table->decimal('sisa_deposit', 15, 2)->nullable();
            $table->boolean('konfirmasi')->default(false);
            $table->timestamps();
            
            // Add foreign key constraint
            $table->foreign('kode_pelanggan')->references('kode_pelanggan')->on('pelanggans')->onDelete('cascade');
            
            // Add index for performance
            $table->index(['kode_pelanggan', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_pelanggan');
    }
};
