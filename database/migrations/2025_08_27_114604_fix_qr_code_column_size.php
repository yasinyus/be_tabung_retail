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
        // Fix QR code column size for all tables
        Schema::table('tabungs', function (Blueprint $table) {
            $table->longText('qr_code')->nullable()->change();
        });
        
        Schema::table('armadas', function (Blueprint $table) {
            $table->longText('qr_code')->nullable()->change();
        });
        
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->longText('qr_code')->nullable()->change();
        });
        
        Schema::table('gudangs', function (Blueprint $table) {
            $table->longText('qr_code')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabungs', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->change();
        });
        
        Schema::table('armadas', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->change();
        });
        
        Schema::table('pelanggans', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->change();
        });
        
        Schema::table('gudangs', function (Blueprint $table) {
            $table->string('qr_code')->nullable()->change();
        });
    }
};
