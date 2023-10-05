<?php

namespace App\Models;

use App\Models\FileRemark;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $with = ['file_users', 'remarks', 'audit_report', 'histories', 'items'];

    protected $appends = ['shared_users'];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function directory()
    {
        return $this->belongsTo(Directory::class);
    }

    public function file_users()
    {
        return $this->hasMany(FileUser::class);
    }

    public function histories()
    {
        return $this->hasMany(FileHistory::class)->orderBy('created_at', 'DESC');
    }

    public function getSharedUsersAttribute()
    {
        return implode(', ', $this->file_users->pluck('user_id')->toArray() ?? []);
    }

    public function remarks()
    {
        return $this->hasMany(FileRemark::class)->latest();
    }

    public function audit_report()
    {
        return $this->hasOne(AuditReport::class);
    }

    public function manual()
    {
        return $this->hasOne(Manual::class);
    }

    public function trackings()
    {
        $track_records = [];
        if($this->type == 'evidences') {
            $track_records = $this->trackItem($this->id,
                [
                    [
                        'name' => 'DCC',
                        'role' => 'Document Control Custodian',
                        'color' => 'bg-success'
                    ],
                    [
                        'name' => 'Auditor',
                        'role' => 'Internal Auditor',
                        'color' => 'bg-danger'
                    ],
                ]
            );
        }
        if($this->type == 'audit_reports') {
            $track_records = $this->trackItem($this->id,
                [
                    [
                        'name' => 'Auditor',
                        'role' => 'Internal Auditor',
                        'color' => 'bg-success'
                    ],
                    [
                        'name' => 'Lead Auditor',
                        'role' => 'Internal Lead Auditor',
                        'color' => 'bg-danger'
                    ],
                ]
            );
        }
        if(in_array($this->type, ['manuals', 'survey_reports', 'consolidated_audit_reports'])) {
            $track_records = $this->trackItem($this->id,
                [
                    [
                        'name' => 'Director',
                        'role' => 'Quality Assurance Director',
                        'color' => 'bg-success'
                    ],
                    [
                        'name' => 'CMT',
                        'role' => 'College Management Team',
                        'color' => 'bg-danger'
                    ],
                ]
            );
        }
        return $track_records;
        
    }

    private function trackItem($file_id, $tracks)
    {
        $track_records = [];
        foreach($tracks as $track) {
            

            $remarks = FileRemark::whereHas('user.role', function($q) use($track) {
                $q->whereIn('role_name', [$track['role']]);
            })->where('file_id', $file_id)->first();

            if(!empty($remarks)) {
                $track_records[] = [
                    'file_id' => $remarks->file_id,
                    'name' => $track['name'],
                    'color' => $track['color'],
                    'date' => $remarks->created_at->format('M d, Y h:i A'),
                    'user' => $remarks->user->firstname .' '.$remarks->user->surname
                ];
            }else{
                $track_records[] = ['name'=> $track['name']];
            }
        }
        return $track_records;
    }

    public function items()
    {
        return $this->hasMany(FileItem::class)->whereNull('file_history_id');
    }
}
