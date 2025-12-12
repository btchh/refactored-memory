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
        // Drop existing table and recreate with new structure
        Schema::dropIfExists('login_attempts');
        
        Schema::create('login_attempts', function (Blueprint $table) {
            $table->id();
            $table->string('identifier'); // username or email used
            $table->string('ip_address', 45);
            $table->unsignedTinyInteger('failed_attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamps();
            
            // Index for quick lookups
            $table->unique(['identifier', 'ip_address']);
            $table->index('last_attempt_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_attempts');
    }
};
