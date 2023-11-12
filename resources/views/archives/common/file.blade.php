<div class="col-2 text-center">
    @if($file->type == 'audit_reports'
        && !empty($file->audit_report)
        && !empty($file->audit_report->cars))
            <a href="{{ route('archives-show-file', $file->audit_report->cars->file->id) }}" class="cars" target="_blank"><img src="{{ asset('media/info.png') }}" width="40px"></a>
    @endif
    <button 
        data-toggle="tooltip" title="{{ $file->directory->fullPath() ?? '' }} > {{ $file->file_name ?? '' }}" 
        class="btn btn-file align-items-center justify-content-center pb-0" data-bs-toggle="dropdown" 
        aria-expanded="false" data-route="{{ route('archives-show-file', $file->id) }}">
            <img src="{{ Storage::url('assets/file.png') }}" alt="filewhite.png" class="img-fluid">
            <p class="text-black mb-0" style="text-overflow: ellipsis"><small>{{ $file->file_name ?? '' }}</small></p>
    </button>

    @if(in_array($file->type, ['evidences', 'templates', 'manuals', 'audit_reports', 'consolidated_audit_reports', 'survey_reports']))
        <button class="btn btn-remarks
            {{ !empty($file->remarks) ? 'btn-success' : 'btn-secondary' }}
            {{ $file->type == 'survey_reports' && $file->survey_report->status == 'rejected' ? 'btn-danger' : '' }}
            {{ $file->type == 'consolidated_audit_reports' && $file->cons_audit_report->status == 'rejected' ? 'btn-danger' : '' }}
            " data-bs-toggle="modal" data-bs-target="#remarksModal"
            data-file-id="{{ $file->id }}"
            {{ (in_array(Auth::user()->role->role_name, ['Internal Auditor', 'Internal Lead Auditor', 'Staff', 'Document Control Custodian', 'College Management Team', 'Quality Assurance Director']))
            ? 'data-route='.route('save-remarks', $file->id) : '' }}>
                    <small><i class="fa fa-envelope"></i> Remarks</small>
        </button>
    @endif
    @include('archives.common.file_dropdown')
</div>