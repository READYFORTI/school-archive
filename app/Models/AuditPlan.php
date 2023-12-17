<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPlan extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];

    protected $dates = ['audit_plan_date'];
    
    public function areas()
    {
        return $this->hasManyThrough(
            Area::class,
            AuditPlanArea::class,
            'audit_plan_id',
            'id',
            'id',
            'area_id'
        );
    }

    public function plan_areas()
    {
        return $this->hasMany(AuditPlanArea::class);
    }

    public function audit_plan_file() {
        return $this->hasOne(AuditPlanFile::class,'audit_plan_id','id');
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            AuditPlanAreaUser::class,
            'audit_plan_id',
            'id',
            'id',
            'user_id'
        );
    }

    public function plan_users()
    {
        return $this->hasMany(AuditPlanAreaUser::class);
    }

    public function batches()
    {
        return $this->hasMany(AuditPlanBatch::class);
    }
}
