<?php

namespace App\Traits;

use DB;

trait Access {

    public function getModulesAccess($allAccess = false) 
    {
        $ret = [];

        foreach (MODULES_LIST AS $module) {

            $basic  = $this->getAllModuleAccess($module);    
            $global = $this->getGloballAccess($module);
    
            if (($allAccess) && ($this->metaData['model'] == 'User')) {

                $role        = $this->role[0];
                $role_basic  = $role->getAllModuleAccess($module);
                $role_global = $role->getGloballAccess($module);

                foreach (CRUD_ACTIONS AS $action) {
                    
                    if (!$basic[$action]) {
                        $basic[$action] = $role_basic[$action];
                    }

                    if (!$global[$action]) {
                        $global[$action] = $role_global[$action];
                    }

                }

                foreach ($this->groups AS $group) {
                    
                    $group_basic  = $group->getAllModuleAccess($module);
                    $group_global = $group->getGloballAccess($module);

                    foreach (CRUD_ACTIONS AS $action) {
                    
                        if (!$basic[$action]) {
                            $basic[$action] = $group_basic[$action];
                        }
    
                        if (!$global[$action]) {
                            $global[$action] = $group_global[$action];
                        }
    
                    }
                    
                }

            }  

            $ret[] = [
                'id'      => createUniqueIdForApollo($this->metaData['model'], $this->id, $module, 'access-module'),
                'model'   => $module,
                'actions' => [
                    'basic'  => $basic,
                    'global' => $global
                ]
            ];

        }

        return $ret;
    }

    public function getAllModuleAccess($module, $action = '') 
    {
        $access = [];

        foreach (CRUD_ACTIONS AS $crud_action) {

            if ((!empty($action)) && ($crud_action != $action)) {
                continue;
            }

            $module_acc = $this->modules()->where('module', $module)->where('action', $crud_action)->get();

            $access[$crud_action] = ($module_acc->count()) ? true : false;

        }

        return $access;
    }

    public function getGloballAccess($model, $action = '') 
    {
        $global = [];

        foreach (CRUD_ACTIONS AS $crud_action) {

            if ((!empty($action)) && ($crud_action != $action)) {
                continue;
            }

            $global_perm = $this->permissions()->where('model', $model)->where('model_id', 0)->where('action', $crud_action)->get();

            $global[$crud_action] = ($global_perm->count()) ? true : false;

        }

        return $global;
    }

    public function getFilteredModulesAccess($module, $action, $allAccess = false) 
    {
        $ret = [];

        $basic  = $this->getAllModuleAccess($module, $action);    
        $global = $this->getGloballAccess($module, $action);

        if (($allAccess) && ($this->metaData['model'] == 'User')) {

            $role        = $this->role[0];
            $role_basic  = $role->getAllModuleAccess($module, $action);
            $role_global = $role->getGloballAccess($module, $action);
                
            if (!$basic[$action]) {
                $basic[$action] = $role_basic[$action];
            }

            if (!$global[$action]) {
                $global[$action] = $role_global[$action];
            }

            foreach ($this->groups AS $group) {
                
                $group_basic  = $group->getAllModuleAccess($module, $action);
                $group_global = $group->getGloballAccess($module, $action);
                
                if (!$basic[$action]) {
                    $basic[$action] = $group_basic[$action];
                }

                if (!$global[$action]) {
                    $global[$action] = $group_global[$action];
                }
                
            }

        }  

        $ret = [
            'action' => $action,
            'basic'  => $basic[$action],
            'global' => $global[$action]
        ];


        return $ret;
    }

}