<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class TaskToUsers extends Model
{
    use UsesTenantConnection;
    //
}
