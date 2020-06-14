<?php

namespace App\Tenant;

use App\Traits\Access;
use App\Traits\Collaborators;

use App\Tenant\User;
use App\Tenant\Project;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class Task extends Model
{
    use UsesTenantConnection, Collaborators, Access;

    protected $fillable = [
        'name', 'description', 'deadline', 'status', 'project_id', 'owner_id'
    ];

    protected $metaData = [
        'table' => 'tasks',
        'model' => 'Task',
    ];
    
    /**
     * Project creator
     */
    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    /**
     * The users that have this task assigned.
     */
    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_to_users');
    }

    /**
     * Main project for this task
     */
    public function project()
    {
        return $this->hasOne(Project::class, 'id', 'project_id');
    }

    public function statusText()
    {
        if (!empty(TASK_STATUS[$this->status])) {
            return TASK_STATUS[$this->status];
        }

        return TASK_STATUS[0];
    }
    
    public static function createTask($name, $description, $deadline, $status, $assignees, $project)
    {
        $status = (!empty(TASK_STATUS_INVERSE[$status])) ? TASK_STATUS_INVERSE[$status] : 0;

        $newTask = Task::create([
            'name'        => $name,
            'description' => $description,
            'deadline'    => $deadline,
            'status'      => $status,
            'project_id'  => $project,
            'owner_id'    => auth()->user()->id
        ]);

        foreach ($assignees AS $a_id) {

            $assignee = User::find($a_id);

            if ($assignee) {
                $newTask->assignees()->attach($assignee->id);
            }

        }

        $newTask->save();

        addRules('Task', $newTask->id);

        return $newTask;
    }

    public static function updateTask($taskId, $name, $description, $deadline, $status, $assignees, $project)
    {
        $task   = Task::find($taskId);
        $status = (!empty(TASK_STATUS_INVERSE[$status])) ? TASK_STATUS_INVERSE[$status] : 0;

        $task->name        = $name;
        $task->description = $description;
        $task->deadline    = $deadline;
        $task->status      = $status;
        $task->project_id  = $project;

        $task->assignees()->detach();

        foreach ($assignees AS $a_id) {

            $assignee = User::find($a_id);

            if ($assignee) {
                $task->assignees()->attach($assignee->id);
            }

        }

        $task->save();

        return $task;
    }
}
