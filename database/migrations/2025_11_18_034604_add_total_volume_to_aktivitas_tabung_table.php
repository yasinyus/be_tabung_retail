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
            $table->decimal('total_volume', 10, 2)->nullable()->after('total_tabung');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aktivitas_tabung', function (Blueprint $table) {
            $table->dropColumn('total_volume');
        });
    }
};
