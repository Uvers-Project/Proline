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
        // 1. Rename table
        Schema::rename('monthly_tasks', 'tasks');

        // 2. Modify columns
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('tasks')->onDelete('cascade');
            
            // Make monthly_plan_id nullable (as it's now optional)
            $table->unsignedBigInteger('monthly_plan_id')->nullable()->change();
            
            // We change the enum to a string to allow the 5 new statuses
            $table->string('status')->default('Backlog')->change();
        });
        
        // 3. Optional: Move tasks to have project_id based on monthly_plan's project
        \Illuminate\Support\Facades\DB::statement('UPDATE tasks SET project_id = (SELECT project_id FROM monthly_plans WHERE monthly_plans.id = tasks.monthly_plan_id) WHERE monthly_plan_id IS NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
            $table->dropColumn('start_date');
            $table->dropColumn('end_date');
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
            
            $table->unsignedBigInteger('monthly_plan_id')->nullable(false)->change();
            // Assuming old enum was: 'Planned', 'In Progress', 'Done', 'Cancelled'
            // Downgrading might be tricky, so we just let it be a string for simplicity in down()
        });

        Schema::rename('tasks', 'monthly_tasks');
    }
};
