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
        Schema::table('weekly_meeting_tasks', function (Blueprint $table) {
            $table->dropColumn('is_new_task');
            $table->string('relation_type')->default('completed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('weekly_meeting_tasks', function (Blueprint $table) {
            $table->dropColumn('relation_type');
            $table->boolean('is_new_task')->default(false);
        });
    }
};
