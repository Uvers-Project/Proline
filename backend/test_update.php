<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $project = App\Models\Project::find(11);
    $project->tasks()->whereIn('status', ['Done', 'Blocked'])->update(['status' => 'In Progress']);
    echo 'Success';
} catch (\Throwable $e) {
    echo 'Error: ' . $e->getMessage();
}

