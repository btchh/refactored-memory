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
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('admin_name');
            $table->string('fname');
            $table->string('lname');
            $table->string('address');
            $table->string('branch_address')->nullable(); // Branch/Shop location
            $table->decimal('branch_latitude', 10, 8)->nullable();
            $table->decimal('branch_longitude', 11, 8)->nullable();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamp('location_updated_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
