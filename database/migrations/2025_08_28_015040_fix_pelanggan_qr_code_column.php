<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to ensure column type is changed properly
        DB::statement('ALTER TABLE pelanggans MODIFY COLUMN qr_code LONGTEXT NULL');
        DB::statement('ALTER TABLE tabungs MODIFY COLUMN qr_code LONGTEXT NULL');
        DB::statement('ALTER TABLE armadas MODIFY COLUMN qr_code LONGTEXT NULL');
        DB::statement('ALTER TABLE gudangs MODIFY COLUMN qr_code LONGTEXT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE pelanggans MODIFY COLUMN qr_code VARCHAR(255) NULL');
        DB::statement('ALTER TABLE tabungs MODIFY COLUMN qr_code VARCHAR(255) NULL');
        DB::statement('ALTER TABLE armadas MODIFY COLUMN qr_code VARCHAR(255) NULL');
        DB::statement('ALTER TABLE gudangs MODIFY COLUMN qr_code VARCHAR(255) NULL');
    }
};
