<?php

namespace App\Tenant;

use App\Traits\Access;
use App\Traits\Collaborators;

use App\Tenant\User;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class Project extends Model
{
    use UsesTenantConnection, Collaborators, Access;
    
    protected $fillable = [
        'name', 'description', 'owner_id'
    ];

    protected $metaData = [
        'table' => 'projects',
        'model' => 'Project',
    ];

    /**
     * Project creator
     */
    public function owner()
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }

    /**
     * Project manager
     */
    public function managers()
    {
        return $this->belongsToMany(User::class, 'managers_to_projects', 'project_id', 'manager_id');
    }

    /**
     * Tasks
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'id');
    }

    public function getAssignees() 
    {
        $ret = [];

        foreach ($this->tasks AS $task) {

            $_assignees = $task->assignees->pluck('id', 'id')->toArray();

            foreach ($_assignees AS $assignee) {
                $ret[$assignee] = $assignee;
            }

        }

        if (empty($ret)) {
            return [];
        }

        return User::whereRaw('id IN (' . implode(',', $ret) . ')')->get();

    }

    public function isAssignee($userId)
    {
        foreach ($this->tasks AS $task) {

            if ($task->assignees->contains($userId)) {
                return true;
            }

        }

        return false;
    }

    public function userHasAssociation($userId, $includeAssignees = false) 
    {
        if ($includeAssignees) {

            if (
                ($this->owner_id == $userId) || // je owner
                ($this->managers->contains($userId)) || // alebo je manager
                ($this->isAssignee($userId)) // alebo je assignee 
            ) {
                return true;
            }

        } else {

            if (
                ($this->owner_id == $userId) || // je owner
                ($this->managers->contains($userId)) // alebo je manager
            ) {
                return true;
            }

        }

        return false;
    }

    public static function createProject($name, $description, $managersId)
    {
        $newProject = Project::create([
            'name'        => $name,
            'description' => $description,
            'owner_id'    => auth()->user()->id
        ]);

        foreach ($managersId AS $m_id) {

            $manager = User::find($m_id);

            if ($manager) {
                $newProject->managers()->attach($manager->id);
            }

        }

        $newProject->save();

        addRules('Project', $newProject->id);

        return $newProject;
    }

    public static function updateProject($projectId, $name, $description, $managersId)
    {
        $project = Project::find($projectId);

        $project->name        = $name;
        $project->description = $description;

        $project->managers()->detach();

        foreach ($managersId AS $m_id) {

            $manager = User::find($m_id);

            if ($manager) {
                $project->managers()->attach($manager->id);
            }

        }

        $project->save();

        return $project;
    }
    
}
