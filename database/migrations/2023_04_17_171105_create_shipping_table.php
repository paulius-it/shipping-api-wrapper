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
        Schema::create('shipping', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number');
            $table->foreignId('provider_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('sender_address');
            $table->string('receiver_address');
            $table->string('phone');
            $table->string('receiver_email');
            $table->string('item');
            $table->integer('quantity');
$table->enum('status', ['Sent', 'Delivered', 'In_progress', 'Failed', 'Canceled'])->default('Sent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping');
    }
};
