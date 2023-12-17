<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPlanArea extends Model
{
    use HasFactory;
    
    protected $with = ['area'];
    
    protected $guarded = [];
    
    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function users()
    {
        return $this->hasManyThrough(
            User::class,
            AuditPlanAreaUser::class,
            'audit_plan_area_id',
            'id',
            'id',
            'user_id'
        );
    }

    public function lead() {
        return $this->hasOneThrough(User::class,AuditPlanArea::class,'lead_user_id','id','id','user_id');
    }

    public function area_users() {
        return $this->hasMany(AuditPlanAreaUser::class);
    }
}
