<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class ModuleAccess extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'module', 'action'
    ];

    public static function createModule($moduleName)
    {
        foreach (CRUD_ACTIONS AS $action) {

            ModuleAccess::create([
                'module' => $moduleName,
                'action' => $action
            ]);

        }
        
    }
}
