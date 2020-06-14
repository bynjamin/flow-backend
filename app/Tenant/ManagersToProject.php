<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class ManagersToProject extends Model
{
    use UsesTenantConnection;
    //
}
