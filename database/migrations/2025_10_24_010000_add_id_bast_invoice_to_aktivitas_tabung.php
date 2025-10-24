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
        Schema::table('aktivitas_tabung', function (Blueprint $table) {
            if (!Schema::hasColumn('aktivitas_tabung', 'id_bast_invoice')) {
                $table->string('id_bast_invoice', 100)->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aktivitas_tabung', function (Blueprint $table) {
            if (Schema::hasColumn('aktivitas_tabung', 'id_bast_invoice')) {
                $table->dropColumn('id_bast_invoice');
            }
        });
    }
};
