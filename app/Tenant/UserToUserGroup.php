<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class UserToUserGroup extends Model
{
    use UsesTenantConnection;
    
    protected $table = 'user_to_group';
}
