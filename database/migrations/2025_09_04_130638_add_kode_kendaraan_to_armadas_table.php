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
        Schema::table('armadas', function (Blueprint $table) {
            $table->string('kode_kendaraan', 20)->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('armadas', function (Blueprint $table) {
            $table->dropColumn('kode_kendaraan');
        });
    }
};
