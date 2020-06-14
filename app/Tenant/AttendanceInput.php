<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class AttendanceInput extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'attendance_id', 'type', 'time'
    ];

    public function attendace()
    {
        return $this->hasOne(Attendance::class, 'id', 'attendance_id');
    }
}
