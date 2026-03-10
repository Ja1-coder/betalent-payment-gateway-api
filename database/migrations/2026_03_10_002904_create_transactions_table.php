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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('gateway_id')->constrained('gateways');
            $table->foreignId('product_id')->constrained('products'); 
            
            $table->string('external_id')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('amount');
            $table->integer('quantity');
            $table->string('card_last_numbers', 4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
