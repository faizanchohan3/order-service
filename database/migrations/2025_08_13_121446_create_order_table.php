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
        Schema::create('order', function (Blueprint $table) {
            $table->id(); // Order ki unique ID
            $table->unsignedBigInteger('user_id'); // Order denay walay user ki ID (UserService se)
            $table->unsignedBigInteger('restaurant_id'); // Restaurant ki ID (RestaurantService se)
            $table->decimal('total_amount', 10, 2); // Order ka total bill
            $table->string('status')->default('pending'); // Order ka status (e.g., pending, processing, delivered)
            $table->timestamps(); // 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
