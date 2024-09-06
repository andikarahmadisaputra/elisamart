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
        Schema::create('topup_user_headers', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->unsignedbigInteger('store_id');
            $table->unsignedInteger('total_user');
            $table->decimal('total_amount', total: 12, places: 2)->default(0);
            $table->string('note')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'under review', 'canceled'])->default('pending');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_user_headers');
    }
};
