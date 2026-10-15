<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // instant, sameday, regular, pickup
            $table->text('description')->nullable();
            $table->decimal('cost', 12, 2)->default(0.00);
            $table->string('estimated_time')->default('1-2 Hari');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. "DANA Official", "QRIS Standar Nasional"
            $table->string('code')->unique(); // dana, qris, bca_manual
            $table->string('account_name')->nullable(); // Nama Pemilik Rekening / Merchant
            $table->string('account_number')->nullable(); // Nomor Akun DANA / Rekening
            $table->string('qr_image_path')->nullable(); // QR Image uploaded by Admin
            $table->text('instructions')->nullable(); // Petunjuk pembayaran step-by-step
            $table->integer('expiry_minutes')->default(120); // Batas waktu transfer
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
        Schema::dropIfExists('shipping_methods');
    }
};
