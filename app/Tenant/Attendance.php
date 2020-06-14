<?php

namespace App\Tenant;

use Carbon\Carbon;
use App\Tenant\AttendanceInput;
use Illuminate\Database\Eloquent\Model;

use Hyn\Tenancy\Traits\UsesTenantConnection;

class Attendance extends Model
{
    use UsesTenantConnection;

    protected $fillable = [
        'user_id'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function attendanceInputs()
    {
        return $this->hasMany(AttendanceInput::class, 'attendance_id', 'id');
    }

    public function start()
    {
        return $this->attendanceInputs()->where('type', ATTENDANCE_TYPE['arrival'])->first();
    }

    public function end()
    {
        return $this->attendanceInputs()->where('type', ATTENDANCE_TYPE['leave'])->first();
    }

    public function arrival()
    {
        $attendanceInput = AttendanceInput::create([
            'attendance_id' => $this->id,
            'type'          => ATTENDANCE_TYPE['arrival'],
            'time'          => Carbon::now()
        ]);
    }

    public function leave()
    {
        $attendanceInput = AttendanceInput::create([
            'attendance_id' => $this->id,
            'type'          => ATTENDANCE_TYPE['leave'],
            'time'          => Carbon::now()
        ]);
    }

    public function total()
    {
        $start = $this->start();
        $end   = $this->end();

        if (!$start) {
            return 0;
        }

        if (!$end) {
            return 0;
        }

        $start_time = new Carbon($start->time);
        $end_time   = new Carbon($end->time);

        $diff         = $start_time->diffInMinutes($end_time);
        $diff_hours   = round($diff / 60);
        $diff_minutes = $diff - ($diff_hours * 60);
        
        $diff_hours   = ($diff_hours < 10)   ? '0' . $diff_hours   : $diff_hours;
        $diff_minutes = ($diff_minutes < 10) ? '0' . $diff_minutes : $diff_minutes;

        return $diff_hours . ':' . $diff_minutes;
    }
}
