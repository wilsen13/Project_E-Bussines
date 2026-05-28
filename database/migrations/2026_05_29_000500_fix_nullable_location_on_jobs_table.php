<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix: Make locationID truly nullable and re-add FK with onDelete('set null').
     */
    public function up(): void
    {
        // Step 1: Drop the existing foreign key constraint (if it exists)
        try {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropForeign(['locationID']);
            });
        } catch (\Exception $e) {
            // FK may already be dropped from a partial previous run — continue
        }

        // Step 2: Make the column nullable via raw SQL (avoids doctrine/dbal requirement)
        DB::statement('ALTER TABLE jobs MODIFY locationID CHAR(36) NULL');

        // Step 3: Fix any orphaned rows that reference non-existent locations
        DB::statement('UPDATE jobs SET locationID = NULL WHERE locationID IS NOT NULL AND locationID NOT IN (SELECT locationID FROM locations)');

        // Step 4: Re-add the foreign key with onDelete set null
        Schema::table('jobs', function (Blueprint $table) {
            $table->foreign('locationID')
                  ->references('locationID')
                  ->on('locations')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropForeign(['locationID']);
        });

        DB::statement('ALTER TABLE jobs MODIFY locationID CHAR(36) NOT NULL');

        Schema::table('jobs', function (Blueprint $table) {
            $table->foreign('locationID')
                  ->references('locationID')
                  ->on('locations');
        });
    }
};
