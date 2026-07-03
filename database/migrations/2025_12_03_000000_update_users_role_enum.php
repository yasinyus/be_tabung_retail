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
        // For MySQL, we need to use raw SQL to modify enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_utama', 'admin_umum', 'kepala_gudang', 'operator_retail', 'operator', 'driver', 'auditor', 'agen', 'keuangan', 'pelanggan_umum', 'pelanggan_agen') DEFAULT 'operator_retail'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin_utama', 'admin_umum', 'kepala_gudang', 'operator_retail', 'driver') DEFAULT 'operator_retail'");
    }
};
