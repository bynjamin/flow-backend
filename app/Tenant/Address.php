<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class Address extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'street', 'zip', 'city', 'country'
    ];
}
