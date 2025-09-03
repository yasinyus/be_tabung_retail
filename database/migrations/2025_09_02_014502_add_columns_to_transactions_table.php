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
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('trx_id', 50)->unique()->after('id');
            $table->unsignedBigInteger('user_id')->after('trx_id');
            $table->unsignedBigInteger('customer_id')->nullable()->after('user_id');
            $table->dateTime('transaction_date')->useCurrent()->after('customer_id');
            $table->enum('type', ['sale', 'purchase', 'refund'])->default('sale')->after('transaction_date');
            $table->decimal('total', 15, 2)->after('type');
            $table->enum('payment_method', ['cash', 'transfer', 'ewallet', 'credit'])->after('total');
            $table->enum('status', ['pending', 'paid', 'cancelled', 'refunded'])->default('pending')->after('payment_method');
            $table->text('notes')->nullable()->after('status');
            
            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('pelanggans')->onDelete('set null');
            
            // Indexes
            $table->index('trx_id');
            $table->index('user_id');
            $table->index('customer_id');
            $table->index('transaction_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['customer_id']);
            $table->dropIndex(['trx_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['customer_id']);
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['status']);
            
            $table->dropColumn([
                'trx_id',
                'user_id',
                'customer_id',
                'transaction_date',
                'type',
                'total',
                'payment_method',
                'status',
                'notes'
            ]);
        });
    }
};
