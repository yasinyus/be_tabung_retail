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
        Schema::table('laporan_pelanggan', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_pelanggan', 'id_bast_invoice')) {
                $table->string('id_bast_invoice')->nullable()->after('keterangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_pelanggan', function (Blueprint $table) {
            $table->dropColumn('id_bast_invoice');
        });
    }
};
