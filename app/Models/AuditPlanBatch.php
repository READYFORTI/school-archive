<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPlanBatch extends Model
{
    use HasFactory;
    
    protected $guarded = [];
    
    protected $appends = ['area_names'];

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            AuditPlanAreaUser::class,
            'audit_plan_batch_id',
            'id',
            'id',
            'user_id'
        );
    }

    public function batch_users() {
        return $this->hasMany(AuditPlanAreaUser::class);
    }

    public function areas()
    {
        return $this->hasManyThrough(
            Area::class,
            AuditPlanArea::class,
            'audit_plan_batch_id',
            'id',
            'id',
            'area_id'
        );
    }

    public function areaLead(){
        return $this->hasMany(AuditPlanArea::class,'audit_plan_batch_id','id');
    }

    public function area_names()
    {
        $names = [];
        foreach($this->areas->unique() as $area) {
            $names[] = $area->parent->area_name .' > '. $area->area_name;
        }
        return implode(', ', $names);
    }

    public function user_names()
    {
        $names = [];
        foreach($this->users->unique() as $user) {
            $names[] = $user->firstname .' '. $user->surname;
        }
        return implode(', ', $names);
    }

    public function getAreaNamesAttribute()
    {
        return $this->area_names();
    }
}
