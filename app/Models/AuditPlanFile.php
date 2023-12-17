<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditPlanFile extends Model
{
    use HasFactory;
    public $table = 'audit_plan_files';
    public $timestamps = false;
}
