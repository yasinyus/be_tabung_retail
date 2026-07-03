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
        Schema::table('refunds', function (Blueprint $table) {
            if (!Schema::hasColumn('refunds', 'volume')) {
                $table->decimal('volume', 10, 2)->nullable()->after('bast_id');
            }
            if (!Schema::hasColumn('refunds', 'kode_pelanggan')) {
                $table->string('kode_pelanggan')->nullable()->after('volume');
            }
            if (!Schema::hasColumn('refunds', 'harga_per_m3')) {
                $table->decimal('harga_per_m3', 10, 2)->nullable()->after('kode_pelanggan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('refunds', function (Blueprint $table) {
            $table->dropColumn(['volume', 'kode_pelanggan', 'harga_per_m3']);
        });
    }
};
