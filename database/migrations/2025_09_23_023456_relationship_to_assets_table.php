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
        Schema::table('assets', function (Blueprint $table) {
            $table->foreignId('category_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null')
                ->after('id');

            $table->foreignId('location_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null')
                ->after('category_id');

            $table->foreignId('manufacturer_id')
                ->nullable()
                ->constrained()
                ->onDelete('set null')
                ->after('location_id');

            $table->foreignId('assigned_to_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropForeign(['location_id']);
            $table->dropForeign(['manufacturer_id']);
            $table->dropForeign(['assigned_to_user_id']);

            $table->dropColumn(['category_id', 'location_id', 'manufacturer_id', 'assigned_to_user_id']);
        });
    }
};
