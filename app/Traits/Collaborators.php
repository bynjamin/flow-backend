<?php

namespace App\Traits;

use DB;
use App\Tenant\User;
use App\Tenant\UserGroup;
use App\Tenant\Permission;

trait Collaborators {

    public function permissionToThis()
    {
        if (empty($this->metaData['model'])) {
            return [];
        }

        return Permission::where('model', $this->metaData['model'])
                    ->where('model_id', $this->id)
                    ->get()
                    ->pluck('action', 'id')->toArray();
    }

    public function getUserCollaborators($perms)
    {   
        if (empty($perms)) {
            return [];
        }

        $perms_ids = implode(',', array_keys($perms));

        $user_ids = DB::connection($this->getConnectionName())
                        ->table('users')
                        ->select('users.id')
                        ->leftJoin('user_to_permissions AS u2p', 'users.id', '=', 'u2p.user_id')
                        ->whereRaw('u2p.permission_id IN (' . $perms_ids . ')')
                        ->groupBy('u2p.user_id')
                        ->get()
                        ->pluck('id')
                        ->toArray();

        $users = User::find($user_ids);

        if (!$users) {
            return [];
        }

        $ret = [];

        foreach ($users AS $user) {
            
            $user_perms = $user->permissions()->whereRaw('permissions.id IN (' . $perms_ids . ')')->get();

            $rules = [
                'create' => false,
                'read'   => false,
                'update' => false,
                'delete' => false
            ];

            foreach ($user_perms AS $perm) {
                $rules[$perm->action] = true;
            }

            $ret[] = [
                'user'  => $user,
                'rules' => $rules
            ];
        }

        return $ret;
    }

    public function getGroupCollaborators($perms)
    {
        if (empty($perms)) {
            return [];
        }

        $perms_ids = implode(',', array_keys($perms));

        $groups_ids = DB::connection($this->getConnectionName())
                        ->table('user_groups')
                        ->select('user_groups.id')
                        ->leftJoin('user_group_to_permissions AS ug2p', 'user_groups.id', '=', 'ug2p.user_group_id')
                        ->whereRaw('ug2p.permission_id IN (' . $perms_ids . ')')
                        ->where('user_groups.is_role', 0)
                        ->groupBy('ug2p.user_group_id')
                        ->get()
                        ->pluck('id')
                        ->toArray();

        $groups = UserGroup::find($groups_ids);

        if (!$groups) {
            return [];
        }

        $ret = [];

        foreach ($groups AS $group) {
            
            $group_perms = $group->permissions()->whereRaw('permissions.id IN (' . $perms_ids . ')')->get();

            $rules = [
                'create' => false,
                'read'   => false,
                'update' => false,
                'delete' => false
            ];

            foreach ($group_perms AS $perm) {
                $rules[$perm->action] = true;
            }

            $ret[] = [
                'userGroup'  => $group,
                'rules'      => $rules
            ];
        }

        return $ret;
    }

    public function getCollaborators() 
    {
        $userCollaborators  = $this->getUserCollaborators($this->permissionToThis());
        $groupCollaborators = $this->getGroupCollaborators($this->permissionToThis());

        return [
            'users'      => $userCollaborators,
            'userGroups' => $groupCollaborators
        ];

    }

}