<?php

namespace App\Tenant;

use DB;

use App\Traits\Access;
use App\Traits\Collaborators;

use App\Tenant\InvitedUser;
use App\Tenant\ModuleAccess;

use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Hyn\Tenancy\Traits\UsesTenantConnection;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject
{
    use UsesTenantConnection, Collaborators, Access;

    protected $metaData = [
        'table' => 'users',
        'model' => 'User',
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'password', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getToken() {
        return $this->api_token;
    }

    public function destroyToken() {
        $this->update( ['api_token' => null] );
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The groups that belong to the user.
     */
    public function groups()
    {
        return $this->belongsToMany(UserGroup::class, 'user_to_group')->where('is_role', 0);
    }

    /**
     * The groups that belong to the user.
     */
    public function role()
    {
        return $this->belongsToMany(UserGroup::class, 'user_to_group')->where('is_role', 1);
    }

    /**
     * The permissions that belong to the user.
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_to_permissions');
    }
    
    /**
     * The modules that user have access to (CRUD).
     */
    public function modules()
    {
        return $this->belongsToMany(ModuleAccess::class, 'user_to_module_accesses');
    }

    /**
     * The tasks that user have assigned.
     */
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_to_users');
    }

    /**
     * User attendance
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    /**
     * User last attendance
     */
    public function lastAttendance()
    {
        return $this->attendances()->orderBy('id', 'DESC')->first();
    }

    /**
     * The projects that user created or is manager
     */
    public function projects()
    {
        $projects_ids_m = ManagersToProject::where('manager_id', $this->id)->get()->pluck('project_id', 'project_id')->toArray();
        $projects_ids_o = Project::where('owner_id', $this->id)->get()->pluck('id', 'id')->toArray();
        
        $ids = $projects_ids_m;
        
        foreach ($projects_ids_o AS $p_id) {
        
            $ids[$p_id] = $p_id;
        
        }

        return Project::find($ids);
    }

    // get all projects where user has some connection
    public function myProjects()
    {
        $projects = $this->projects()->pluck('id', 'id')->toArray();

        foreach ($this->tasks()->where('deleted', 0)->get() AS $task) {
            $projects[$task->project_id] = $task->project_id;
        }

        if (empty($projects)) {
            return [];
        }

        return Project::where('deleted', 0)->whereRaw('id IN (' . implode(',', $projects) . ')');
    }

    public function getTasks($projectId = 0)
    {
        // @TODO preverit a opravit
        $tasks = [];

        if ($projectId == 0) {

            $projects = $this->projects();

            foreach ($projects AS $project) {

                $_tasks = $project->tasks()->where('deleted', 0)->get()->pluck('id', 'id')->toArray();

                foreach ($_tasks AS $t) {
                    $tasks[$t] = $t;
                }

            }

            $createdTasks = Task::where('owner_id', $this->id)->get()->pluck('id', 'id')->toArray();

            if (!empty($createdTasks)) {
                
                foreach ($createdTasks AS $t) {
                    $tasks[$t] = $t;
                }

            }

        } else {

            $project = Project::find($projectId);

            if ($project->userHasAssociation($this->id)) {
                $tasks = $project->tasks()->where('deleted', 0)->get()->pluck('id', 'id')->toArray();
            }

        }

        $_tasks = $this->tasks->pluck('id', 'id')->toArray();

        foreach ($_tasks AS $_t) {
            $tasks[$_t] = $_t;
        }

        if (empty($tasks)) {
            return [];
        }

        return Task::whereRaw('id in (' . implode(',', $tasks) . ')');
    }

    public function scopeVisible($query) 
    {
        return $query->where('deleted', 0);
    }

    public static function createUser($mail, $password, $activate = false)
    {
        $user = User::create(['email' => $mail, 'password' => Hash::make($password)]);
        $user->save();
        
        $new_address = Address::create([]);
                
        $user->profile()->create([
            'address_id' => $new_address->id  
        ]);

        $user->role()->attach(DEFAULT_USER_ROLE);
        $user->save();

        $rules = addRules('User', $user->id);

        $user->permissions()->attach($rules);
/*
        if ($activate) {

            $user->isActive = true;
            $user->save();

        }*/

        $user->isActive = true;
        $user->save();

        return $user;
    }

    public static function createUserWithData($mail, $password, $profile, $address, $activate = false)
    {
        $user = User::createUser($mail, $password, $activate);
        
        $user->profile()->update($profile);
        $user->profile->address()->update($address);
        
        return $user;
    }

    public function isAdmin()
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $adminGroup = $this->role->find(ADMIN_USER_GROUP);

        if (!empty($adminGroup)) {
            return true;
        }

        return false;
    }

    public function isSuperAdmin()
    {
        $superAdminGroup = $this->role->find(SUPERADMIN_USER_GROUP);

        if (!empty($superAdminGroup)) {
            return true;
        }

        return false;
    }

    public function getPermsId($permModel = '')
    {
        $perms = $this->getPermissions($permModel);

        return array_keys($perms);
    }

    public function getGroupPerms()
    {
        $perms = [];

        foreach ($this->groups AS $group) {
            $perms = array_merge($perms, $group->permissions()->pluck('permissions.id', 'permissions.id')->toArray());
        }

        return $perms;
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'id');
    }

    public function getPermissions($permModel = '')
    {
        $perms = [];

        foreach ($this->groups AS $group) {
            $perms += $group->getPermissions($permModel);
        }

        if ($this->role) {

            foreach ($this->role AS $role) {
                $perms += $role->getPermissions($permModel);
            }
            
        }

        if (!empty($permModel)) {
            
            $data = $this->permissions()->where('model', $permModel)->get();
            
        } else {
            
            $data = $this->permissions->all();
        }

        foreach($data AS $perm) {

            $perms[$perm->id] = [
                'model'    => $perm->model,
                'model_id' => $perm->model_id,
                'action'   => $perm->action
            ];

        } 

        return $perms;
    }

    public function canPerformAction($permModel, $permAction, $type = "basic")
    {
        if (($type != 'basic') && ($type != 'global')) {
            return false;
        }

        $access = $this->getFilteredModulesAccess($permModel, $permAction, true);
        
        if ($type == 'basic') {
            
            if ($access['basic']) {
                return true;
            }

        } else if ($type == 'global') {

            if (($access['basic']) && ($access['global'])) {
                return true;
            }

        }

        return false;
    }

    public function canViewAll($permModel)
    {
        $access = $this->getFilteredModulesAccess($permModel, 'read', true);
        
        if ($access['global']) {
            return true;
        }

        return false;
    }

    public static function checkAction($permModel, $permAction, $type = 'basic', $throwError = true) 
    {
        if (!$user = auth()->user()) {

            if ($throwError) {
                throw new \GraphQL\Error\Error('Not authorized!');
            } else {
                return false;
            }

        }    
        
        if ($user->canPerformAction($permModel, $permAction, $type)) {

            return true;

        } else {

            if ($throwError) {
                throw new \GraphQL\Error\Error('Insufficient privileges to perform action ' . $permAction);
            } else {
                return false;
            }
        
        }

        return false;  
    }

    public function updatePassword($password)
    {
        $this->password = Hash::make($password);
        $this->save();

        return $this;
    }

    public static function getFQDN()
    {
        $posible_hosts = [
            env('TENANT_URL_BASE'),
            env('TENANT_URL_BASE_LOCAL')
        ];

        $origin  = request()->header('origin');
        $_origin = '';

        foreach ($posible_hosts AS $host) {

            if (!empty($_origin)) {
                continue;
            }

            $_origin_arr = explode($host, $origin);
            
            if ((is_array($_origin_arr)) && (count($_origin_arr) > 1)) {
                $_origin = $_origin_arr[0];
                
                $_origin = str_replace('http://', '', $_origin);
                $_origin = str_replace('https://', '', $_origin);
                $_origin = substr($_origin, 0, -1);
                
                $_origin = trim($_origin);
            }
        
        }

        $origin = $_origin;

        if (!empty($origin)) {

            return $origin;

        }

        return '';
    }

    public static function findByMail($email) 
    {
        $users = User::where('email', $email)->get();

        return $users;        
    }

    public static function inviteUser($newUserEmail, $newPassword, $token)
    {
        $tenantUrl = getTenantFullLink();
        
        $fromEmail = 'no-reply@' . $tenantUrl;
        $fromEmail = 'testmail@corpflow.solutions';

        $subject   = 'Vitajte vo Flow';

        $already_invited = InvitedUser::where('email', $newUserEmail)->first();

        if ($already_invited) {
            $already_invited->delete();
        }

        $invited = InvitedUser::create([
            'email'       => $newUserEmail,
            'inviteToken' => $token
        ]);

        $invited->save();        

        $data = [
            'url'      => $tenantUrl . '/login', //?token=' . $token,
            'password' => $newPassword,
            'email'    => $newUserEmail
        ];

        sendMail(
            $fromEmail, 
            $newUserEmail, 
            $subject, 
            $data, 
            'emails.invite'
        );
    }

    public function updateRole($roleId) 
    {
        $_roleId = $this->role[0]->id;
        
        $this->role()->detach($_roleId);
        $this->role()->attach($roleId);
        $this->save();
    }

}
