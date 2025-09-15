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
        Schema::table('stok_tabung', function (Blueprint $table) {
            $table->decimal('volume', 8, 2)->nullable()->after('status')->comment('Volume tabung dalam liter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_tabung', function (Blueprint $table) {
            $table->dropColumn('volume');
        });
    }
};
