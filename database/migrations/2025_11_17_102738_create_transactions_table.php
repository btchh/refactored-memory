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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('set null');
            
            // Booking/Scheduling fields
            $table->date('booking_date');
            $table->time('booking_time');
            $table->string('pickup_address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('item_type', ['clothes', 'comforter', 'shoes']);
            $table->text('notes')->nullable();
            
            // CalAPI integration
            $table->string('calapi_event_id')->nullable();
            
            // Transaction/Financial fields
            $table->decimal('weight', 10, 2)->nullable()->comment('For documentation only');
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            
            $table->timestamps();

            $table->index('booking_date');
            $table->index('user_id');
            $table->index('status');
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
