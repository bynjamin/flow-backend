<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class UserProfile extends Model
{
    use UsesTenantConnection;
    
    protected $fillable = [
        'user_id', 'address_id', 'firstName', 'lastName', 'title', 'phone', 'about', 'birthday', 'gender', 'gdpr', 'position', 'employedFrom', 'employmentType'
    ];

    public function address()
    {
        return $this->hasOne(Address::class, 'id', 'address_id');
    }

}
