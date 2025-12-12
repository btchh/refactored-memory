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
        Schema::table('login_attempts', function (Blueprint $table) {
            // Drop the old unique constraint
            $table->dropUnique(['identifier', 'ip_address']);
            
            // Add action_type column
            $table->string('action_type', 50)->default('login')->after('identifier');
            
            // Add new unique constraint including action_type
            $table->unique(['identifier', 'ip_address', 'action_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_attempts', function (Blueprint $table) {
            $table->dropUnique(['identifier', 'ip_address', 'action_type']);
            $table->dropColumn('action_type');
            $table->unique(['identifier', 'ip_address']);
        });
    }
};
