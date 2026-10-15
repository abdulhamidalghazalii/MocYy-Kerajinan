<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_method_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('amount', 14, 2);
            
            // Payment statuses: unpaid, pending_review, paid, rejected
            $table->string('status')->default('unpaid')->index();
            $table->string('transaction_ref')->nullable(); // Ref/catatan transaksi transfer
            
            // Verification metadata
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestampTz('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestampsTz();

            $table->index(['order_id', 'status']);
        });

        Schema::create('payment_proofs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->cascadeOnDelete();
            $table->string('file_path'); // stored in private disk: payment_proofs/xxx.jpg
            $table->string('original_filename');
            $table->string('mime_type');
            $table->integer('file_size'); // in bytes
            $table->string('sender_account_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestampTz('uploaded_at');
            $table->timestampsTz();

            $table->index(['payment_id', 'uploaded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_proofs');
        Schema::dropIfExists('payments');
    }
};
