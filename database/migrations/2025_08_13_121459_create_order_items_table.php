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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();

            // Yeh batata hai ke yeh item kis order ka hissa hai
            $table->foreignId('order_id')
                  ->constrained('order')
                  ->onDelete('cascade'); // Agar order delete ho to yeh item bhi delete ho jaye
    
            $table->unsignedBigInteger('menu_item_id'); // Khareeday huay item ki ID (RestaurantService se)
            $table->integer('quantity'); // Kitni tadad mein khareeda
            $table->decimal('price', 8, 2); // Item ki us waqt ki price
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
