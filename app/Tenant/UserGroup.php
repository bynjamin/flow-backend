<?php

namespace App\Tenant;

use DB;
use App\Traits\Access;
use App\Traits\Collaborators;
use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class UserGroup extends Model
{
    use UsesTenantConnection, Collaborators, Access;

    protected $metaData = [
        'table' => 'user_groups',
        'model' => 'UserGroup',
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'is_role'
    ];

    /**
     * The users that belong to the group.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_to_group');
    }

    public function getTotalUsersAttribute()
    {
        return $this->belongsToMany(User::class, 'user_to_group')->count();    
    }

    /**
     * The permissions that belong to the group.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_group_to_permissions');
    }
    
    /**
     * The modules that user group have access to (CRUD).
     */
    public function modules()
    {
        return $this->belongsToMany(ModuleAccess::class, 'user_group_to_module_accesses');
    }

    public function scopeGroup($query) 
    {
        return $query->where('is_role', 0);
    }

    public function scopeRole($query) 
    {
        return $query->where('is_role', 1);
    }

    public function getPermissions($permModel = '')
    {
        if (!empty($permModel)) {
            
            $data = $this->permissions()->where('model', $permModel)->get();
            
        } else {
            
            $data = $this->permissions->all();
        }
        
        $perms = [];

        foreach($data AS $perm) {

            $perms[$perm->id] = [
                'model'    => $perm->model,
                'model_id' => $perm->model_id,
                'action'   => $perm->action
            ];

        } 

        return $perms;
    }

    public static function createNewGroup($name, $desc, $members)
    {
        $group = UserGroup::create([
            'name'        => $name,
            'description' => $desc,
            'is_role'     => 0
        ]);

        foreach ($members AS $member) {

            $user = User::find($member);

            if ($user) {
                $group->users()->attach($member);
            }

        }

        $group->save();

        return $group;
    }

    public static function createNewRole($name, $desc)
    {
        $role = self::create([
            'name'        => $name,
            'description' => $desc,
            'is_role'     => 1
        ]);

        addRules('UserGroup', $role->id);

        return $role;
    }

    public static function deleteItem($itemId)
    {
        $group = UserGroup::find($itemId);

        if ($group) {

            if ($group->users) {
                $group->users()->detach();
            }
    
            $group->delete();
            
        }
    }    

    public function addAllPermissions($model, $modelId) 
    {
        foreach (CRUD_ACTIONS AS $action) {
            self::addPermission($model, $modelId, $action);
        }
    }

    public function addPermission($model, $modelId, $action)
    {
        $permId = Permission::where('model', $model)->where('model_id', $modelId)->where('action', $action)->first();

        if (!$permId) {
            return;
        }

        $this->permissions()->attach($permId);
    }

    public function isRole()
    {
        if ($this->is_role == 1) {
            return true;
        }

        return false;
    }

}
