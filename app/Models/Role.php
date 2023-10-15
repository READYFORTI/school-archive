<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $appends = ['directories'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function getDirectoriesAttribute()
    {
        $directories = ['Manuals'];
        if($this->role_name == 'Staff') {
            $directories = ['Manuals', 'Templates'];
        }elseif(in_array($this->role_name, ['Process Owner', 'Document Control Custodian'])) {
            $directories = ['Manuals', 'Evidences'];
        }elseif(in_array($this->role_name, ['Internal Auditor'])) {
            $directories = ['Audit Reports', 'Evidences'];
        }elseif(in_array($this->role_name, ['Internal Lead Auditor'])) {
            $directories = ['Audit Reports', 'Evidences', 'Consolidated Audit Reports'];
        }elseif($this->role_name == 'Quality Assurance Director') {
            $directories = ['Manuals', 'Audit Reports', 'Survey Reports', 'Consolidated Audit Reports', 'Evidences'];
        }elseif($this->role_name == 'Human Resources') {
            $directories = ['Survey Reports'];
        }elseif($this->role_name == 'College Management Team') {
            $directories = ['Consolidated Audit Reports', 'Survey Reports'];
        }

        return $directories;
    }
}