<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipping_method_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            
            // Recipient & shipping details snapshot
            $table->string('recipient_name');
            $table->string('recipient_phone');
            $table->text('shipping_address');
            $table->string('city');
            $table->string('postal_code')->nullable();
            
            // Notes & Personalization
            $table->text('customer_note')->nullable();
            $table->text('greeting_card_note')->nullable();
            
            // Financial calculations (DECIMAL)
            $table->decimal('subtotal', 14, 2);
            $table->decimal('shipping_cost', 14, 2)->default(0.00);
            $table->decimal('discount_amount', 14, 2)->default(0.00);
            $table->decimal('grand_total', 14, 2);
            
            // Statuses (Separated state machines)
            // Payment Status: unpaid, pending_review, paid, rejected, expired, refunded
            $table->string('payment_status')->default('unpaid')->index();
            
            // Order Status: pending_payment, processing, in_production, ready_to_ship, shipped, completed, cancelled
            $table->string('order_status')->default('pending_payment')->index();
            
            $table->string('tracking_number')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestampTz('paid_at')->nullable();
            $table->timestampTz('cancelled_at')->nullable();
            $table->timestampsTz();
            $table->softDeletes();

            $table->index(['user_id', 'order_status']);
            $table->index(['created_at', 'payment_status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            
            // Snapshots to preserve data even if product is modified or deleted
            $table->string('product_name');
            $table->string('variant_info')->nullable();
            $table->string('sku')->nullable();
            $table->decimal('unit_price', 14, 2);
            $table->integer('quantity')->default(1);
            $table->decimal('subtotal', 14, 2);
            $table->text('custom_note')->nullable();
            $table->text('greeting_card_text')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
