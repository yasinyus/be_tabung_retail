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
            // total volume in m3
            if (!Schema::hasColumn('aktivitas_tabung', 'total_volume')) {
                $table->decimal('total_volume', 12, 2)->default(0)->after('total_tabung');
            }

            if (!Schema::hasColumn('aktivitas_tabung', 'total_harga')) {
                $table->decimal('total_harga', 15, 2)->default(0)->after('total_volume');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aktivitas_tabung', function (Blueprint $table) {
            if (Schema::hasColumn('aktivitas_tabung', 'total_harga')) {
                $table->dropColumn('total_harga');
            }
            if (Schema::hasColumn('aktivitas_tabung', 'total_volume')) {
                $table->dropColumn('total_volume');
            }
        });
    }
};
