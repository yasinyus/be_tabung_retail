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
        Schema::create('stok_tabung', function (Blueprint $table) {
            $table->id();
            $table->string('kode_tabung');
            $table->enum('status', ['Kosong', 'Isi'])->default('Kosong');
            $table->string('posisi')->nullable(); // Lokasi tabung berada
            $table->timestamp('tanggal_update')->useCurrent();
            $table->timestamps();
            
            // Foreign key ke tabungs dengan kode_tabung
            $table->foreign('kode_tabung')->references('kode_tabung')->on('tabungs')->onDelete('cascade');
            
            // Index untuk performa
            $table->index(['status', 'posisi']);
            $table->unique('kode_tabung'); // Unique constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stok_tabung');
    }
};
