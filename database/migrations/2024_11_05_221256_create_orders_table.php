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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('order_id')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('sequence_id')->nullable();
            $table->string('integrator_id')->nullable();
            $table->string('shipping_id')->nullable();
            $table->string('marketplace')->nullable();
            $table->string('account')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_series')->nullable();
            $table->date('order_date')->nullable();
            $table->date('release_date')->nullable();
            $table->decimal('sale_value')->nullable();
            $table->decimal('refund_sale')->default(0);
            $table->decimal('commission')->default(0);
            $table->decimal('refund_commission')->default(0);
            $table->decimal('shipping_fee')->default(0);
            $table->decimal('refund_shipping_fee')->default(0);
            $table->decimal('campaigns')->default(0);
            $table->decimal('refund_campaigns')->default(0);
            $table->decimal('taxes')->default(0);
            $table->decimal('refund_taxes')->default(0);
            $table->decimal('other_credits')->default(0);
            $table->decimal('other_debits')->default(0);
            $table->decimal('net_result')->default(0);
            $table->date('sync_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
