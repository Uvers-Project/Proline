<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('project.{id}', function ($user, $id) {
    $isMember = \App\Models\ProjectMember::where('project_id', $id)
                                    ->where('user_id', $user->id)
                                    ->exists();
    $isCreator = \App\Models\Project::where('id', $id)->where('created_by', $user->id)->exists();
    return $isMember || $isCreator;
});
