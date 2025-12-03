<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add branch_address column if it doesn't exist
        if (!Schema::hasColumn('conversations', 'branch_address')) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->string('branch_address')->nullable()->after('user_id');
            });
        }

        // Migrate existing data: copy branch_address from admins table
        DB::statement('
            UPDATE conversations c
            SET branch_address = (
                SELECT a.branch_address FROM admins a WHERE a.id = c.admin_id
            )
            WHERE c.branch_address IS NULL OR c.branch_address = \'\'
        ');

        // Only drop if admin_id column exists
        if (Schema::hasColumn('conversations', 'admin_id')) {
            // Check if foreign key exists before dropping
            $foreignKeys = collect(DB::select("
                SELECT CONSTRAINT_NAME 
                FROM information_schema.TABLE_CONSTRAINTS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'conversations' 
                AND CONSTRAINT_TYPE = 'FOREIGN KEY'
                AND CONSTRAINT_NAME = 'conversations_admin_id_foreign'
            "));
            
            if ($foreignKeys->isNotEmpty()) {
                Schema::table('conversations', function (Blueprint $table) {
                    $table->dropForeign(['admin_id']);
                });
            }

            // Check if unique index exists before dropping
            $uniqueIndex = collect(DB::select("SHOW INDEX FROM conversations WHERE Key_name = 'conversations_user_id_admin_id_unique'"));
            if ($uniqueIndex->isNotEmpty()) {
                Schema::table('conversations', function (Blueprint $table) {
                    $table->dropUnique(['user_id', 'admin_id']);
                });
            }

            // Drop the admin_id index if it exists
            $adminIdIndex = collect(DB::select("SHOW INDEX FROM conversations WHERE Key_name = 'conversations_admin_id_foreign'"));
            if ($adminIdIndex->isNotEmpty()) {
                Schema::table('conversations', function (Blueprint $table) {
                    $table->dropIndex('conversations_admin_id_foreign');
                });
            }

            Schema::table('conversations', function (Blueprint $table) {
                $table->dropColumn('admin_id');
            });
        }

        // Add unique constraint if it doesn't exist
        $indexes = collect(DB::select("SHOW INDEX FROM conversations WHERE Key_name = 'conversations_user_id_branch_address_unique'"));
        if ($indexes->isEmpty()) {
            Schema::table('conversations', function (Blueprint $table) {
                $table->unique(['user_id', 'branch_address']);
            });
        }
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'branch_address']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->foreignId('admin_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        });

        // We can't fully restore the data, but we'll set admin_id to the first admin of each branch
        DB::statement('
            UPDATE conversations c
            SET admin_id = (
                SELECT MIN(a.id) FROM admins a WHERE a.branch_address = c.branch_address
            )
        ');

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropColumn('branch_address');
            $table->unique(['user_id', 'admin_id']);
        });
    }
};
