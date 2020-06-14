<?php

if (!function_exists('addModule')) {
	function addModule($module) {
        
        App\Tenant\ModuleAccess::createModule($module);

	}
}

if (!function_exists('addRules')) {
	function addRules($model, $model_id) {
        
        $rules = CRUD_ACTIONS;
        $ret   = [];

        foreach ($rules AS $rule) {

            $r     = App\Tenant\Permission::createPerm($model, $model_id, $rule, 'Right to ' . $rule . ' entities in model ' . $model);
            $ret[] = $r;

        }

        return $ret;
	}
}

if (!function_exists('addMainRoles')) {
	function addMainRoles() {
        
        // User Groups
        $userGroups = [
            [
                'name'        => 'Superadmin',
                'description' => 'Group of superadmins',
            ], [
                'name'        => 'Admin',
                'description' => 'Group of admins',
            ], [
                'name'        => 'Manager',
                'description' => 'Group of managers',
            ], [
                'name'        => 'Operator',
                'description' => 'Group of operators',
            ], [
                'name'        => 'User',
                'description' => 'Group of users',
            ]
        ];

        foreach ($userGroups as $userGroup) {
            
            App\Tenant\UserGroup::createNewRole($userGroup['name'], $userGroup['description']);

        }
        
        addRoleMainPermissions();
	}
}

if (!function_exists('addRoleMainPermissions')) {
	function addRoleMainPermissions() {
        
        $group = App\Tenant\UserGroup::where('name', 'Superadmin')->first();

        $group->addAllPermissions('User', 0);
        $group->addAllPermissions('UserGroup', 0);
        $group->addAllPermissions('Permission', 0);
        $group->addAllPermissions('Task', 0);
        $group->addAllPermissions('Project', 0);
        $group->addAllPermissions('Attendance', 0);

        $group = App\Tenant\UserGroup::where('name', 'Admin')->first();

        $group->addAllPermissions('User', 0);
        $group->addAllPermissions('UserGroup', 0);
        $group->addAllPermissions('Permission', 0);
        $group->addAllPermissions('Task', 0);
        $group->addAllPermissions('Project', 0);
        $group->addAllPermissions('Attendance', 0);
        
	}
}

if (!function_exists('installModules')) {
	function installModules() {

		addModule('User');
        addModule('UserGroup');
        addModule('Permission');
        addModule('Task');
        addModule('Project');
        addModule('Attendance');

        addRules('User', 0);
        addRules('UserGroup', 0);
        addRules('Permission', 0);
        addRules('Task', 0);
        addRules('Project', 0);
        addRules('Attendance', 0);

	}
}

if (!function_exists('addAdmin')) {
	function addAdmin($email, $password) {
		$admin = App\Tenant\User::createUser($email, $password, true);
	}
}

if (!function_exists('createUniqueIdForApollo')) {
	function createUniqueIdForApollo($model, $modelId, $module, $extra) {
		return md5($modelId . '-' . $model . '2' . $module . '-' . $extra);
	}
}

if (!function_exists('getTenantFullLink')) {
	function getTenantFullLink() {
        return App\Tenant\User::getFQDN() . '.' . env('TENANT_URL_BASE');
	}
}

if (!function_exists('randomPassword')) {
	function randomPassword($lenght = 8) {

        $alphabet    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass        = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache

        for ($i = 0; $i < ($lenght + 1); $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass); //turn the array into a string
	}
}

if (!function_exists('sendMail')) {
	function sendMail($from, $to, $subject, $data, $view) {

        Mail::send($view, $data, function($message) use ($from, $to, $subject) {

            if (is_array($to)) {
                
                foreach ($to AS $_to) {
                    $message->to($_to);
                }

            } else {

                $message->to($to);

            }

            $message->from($from);
            $message->subject($subject);
        });

	}
}