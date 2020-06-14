<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class InvitedUser extends Model
{
    use UsesTenantConnection;
    
    protected $fillable = [
        'email', 'inviteToken'
    ];
}
