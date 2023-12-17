<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $appends = ['full_name'];

    protected $fillable = [
        'firstname',
        'middlename',
        'surname',
        'suffix',
        'username',
        'password',
        'img'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password'
    ];

    protected $with = ['role'];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assigned_area()
    {
        return $this->hasOneThrough(
            Area::class,
            AreaUser::class,
            'user_id',
            'id',
            'id',
            'area_id'
        );
    }

    public function assigned_areas()
    {
        return $this->hasManyThrough(
            Area::class,
            AreaUser::class,
            'user_id',
            'id',
            'id',
            'area_id'
        );
    }

    public function user_areas()
    {
        return $this->hasMany(AreaUser::class);
    }

    public function getAssignedAreas()
    {
        $areas = [];
        foreach($this->assigned_areas()->get() as $area) {
            $areas[] = sprintf("%s%s", $area->parent->area_name ? $area->parent->area_name.' > ' : '', $area->area_name ?? '');
        }
        return $areas;
    }

    public function getFullNameAttribute()
    {
        return $this->firstname.' '.$this->surname;
    }

    public function audit_plan_area_user()
    {
        return $this->hasMany(AuditPlanAreaUser::class);
    }

    public function audit_plan_areas()
    {
        return $this->hasOne(AuditPlanArea::class,'lead_user_id');
    }
}
