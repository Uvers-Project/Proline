<?php
require __DIR__.'/backend/vendor/autoload.php';
$app = require_once __DIR__.'/backend/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$tasks = \App\Models\Task::where('project_id', 1)->with('subtasks')->get();
foreach ($tasks as $task) {
    if ($task->parent_id) echo "Subtask found flat: {$task->title}\n";
    if ($task->subtasks->count() > 0) echo "Task {$task->title} has subtasks\n";
}
