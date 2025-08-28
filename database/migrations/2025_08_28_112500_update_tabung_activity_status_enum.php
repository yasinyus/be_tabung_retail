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
        Schema::table('tabung_activity', function (Blueprint $table) {
            // Drop the existing enum column
            $table->dropColumn('status');
        });

        Schema::table('tabung_activity', function (Blueprint $table) {
            // Recreate the enum column with new values
            $table->enum('status', ['Kosong', 'Isi', 'Datang'])->default('Isi')->after('keterangan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tabung_activity', function (Blueprint $table) {
            // Drop the column
            $table->dropColumn('status');
        });

        Schema::table('tabung_activity', function (Blueprint $table) {
            // Recreate with original values
            $table->enum('status', ['Kosong', 'Isi'])->default('Isi')->after('keterangan');
        });
    }
};
