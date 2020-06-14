<?php

namespace App\Tenant;

use Illuminate\Database\Eloquent\Model;
use Hyn\Tenancy\Traits\UsesTenantConnection;

class Permission extends Model
{
    use UsesTenantConnection;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'model', 'model_id', 'action', 'description'
    ];

    /**
     * The users that belong(that have) to the permission.
     */
    public function users()
    {
        return $this->belongsToMany(UserGroup::class, 'user_to_permissions');
    }

    /**
     * The user groups that belong(that have) to the permission.
     */
    public function groups()
    {
         return $this->belongsToMany(UserGroup::class, 'user_group_to_permissions');
    }

    public function scopeGlobal($query) 
    {
        return $query->where('model_id', 0);
    }

    public static function createPerm($model, $model_id, $action, $desc)
    {
        $rule = self::create([
            'model'       => $model,
            'model_id'    => $model_id,
            'action'      => $action,
            'description' => $desc
        ]);

        return $rule->id;
    }

    public static function canCreate($model, $model_id) {

        $can = self::where('model', $model)->where('model_id', $model_id)->where('action', 'create')->get();

        if ($can->count()) {
            return true;
        }

        return false;
    }

    public static function canRead($model, $model_id) {
        $can = self::where('model', $model)->where('model_id', $model_id)->where('action', 'read')->get();

        if ($can->count()) {
            return true;
        }

        return false;        
    }

    public static function canUpdate($model, $model_id) {
        $can = self::where('model', $model)->where('model_id', $model_id)->where('action', 'update')->get();

        if ($can->count()) {
            return true;
        }

        return false; 
    }

    public static function canDelete($model, $model_id) {
        $can = self::where('model', $model)->where('model_id', $model_id)->where('action', 'delete')->get();

        if ($can->count()) {
            return true;
        }

        return false;        
    }
}
