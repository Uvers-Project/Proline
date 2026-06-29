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
        Schema::table('weekly_meetings', function (Blueprint $table) {
            $table->json('blocked_task_descriptions')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_meetings', function (Blueprint $table) {
            $table->dropColumn('blocked_task_descriptions');
        });
    }
};
