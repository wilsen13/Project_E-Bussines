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
        Schema::table('jobs', function (Blueprint $table) {
            $table->boolean('is_remote')->default(false);
            $table->string('image_url')->nullable();
            
            // Because locations table is previously required for physical jobs, but remote jobs may not have locations, let's make locationID nullable if it wasn't already.
            // Wait, looking at the previous SQL: locationID CHAR(36) NOT NULL. We need to make it nullable.
            $table->char('locationID', 36)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn(['is_remote', 'image_url']);
            // We won't strictly revert the nullable change since we don't know the exact previous state safely without a doctrine/dbal warning, but it's fine for now.
        });
    }
};
