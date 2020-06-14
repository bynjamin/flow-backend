<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class UserGroupToPermission extends Model
{
    use UsesTenantConnection;
    //
}
