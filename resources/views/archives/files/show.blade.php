@extends('layout.sidebar')
@section('title')
<title>File Details</title>
@endsection
@section('page')
    <div class="page-header">
        <h1>File Details</h1>
    </div>
    <div class="m-3 bg-white py-4">
        <div class="row m-3">
            <div class="col-12 mb-4 text-right">
                @if($file->type == 'audit_reports'
                    && !empty($file->audit_report)
                    && !empty($file->audit_report->cars))
                        <a class="btn btn-info mx-2" href="{{ route('archives-download-file', $file->audit_report->cars->file->id) }}">Open CARS</a>
                @endif

                @if(in_array($file->type, ['evidences', 'templates', 'manuals', 'audit_reports', 'consolidated_audit_reports', 'survey_reports']))
                    <button class="btn btn-remarks mx-2
                        {{ !empty($file->remarks) ? 'btn-success' : 'btn-secondary' }}" data-bs-toggle="modal" data-bs-target="#remarksModal"
                        data-file-id="{{ $file->id }}"
                        {{ (in_array(Auth::user()->role->role_name, ['Internal Auditor', 'Internal Lead Auditor', 'Staff', 'Document Control Custodian', 'College Management Team', 'Quality Assurance Director']))
                        ? 'data-route='.route('save-remarks', $file->id) : '' }}>
                                <i class="fa fa-email"></i> Remarks
                    </button>
                @endif

                <button data-toggle="tooltip"  class="btn btn-warning mx-2" data-bs-toggle="dropdown">Action <i class="fa fa-caret-down"></i></button>
                @include('archives.common.file_dropdown')
            </div>
            <div class="col-6 mb-3">
                <label>Name</label><i class="text-danger"> *</i>
                <input class="form-control" type="text" readonly value="{{ $file->file_name }}" readonly>
            </div>
            <div class="col-6">
                <label>Uploaded By</label><i class="text-danger"> *</i><br/>
                <input class="form-control" type="text" readonly value="{{ $file->user->firstname }} {{ $file->user->surname }}">
            </div>
            @if(!empty($file->directory))
            <div class="col-12 mb-3">
                <label>Path</label><i class="text-danger"> *</i>
                <input class="form-control" type="text" readonly value="{{ $file->directory->fullPath() ?? '' }} > {{ $file->file_name ?? '' }}">
            </div>
            @endif
            <div class="col-6 mb-3">
                <label>Created</label><i class="text-danger"> *</i>
                <input class="form-control" type="text" readonly value="{{ $file->created_at->format('F d, Y h:i A') }}">
            </div>
            <div class="col-6 mb-3">
                <label>Updated</label><i class="text-danger"> *</i>
                <input class="form-control" type="text" readonly value="{{ $file->created_at->format('F d, Y h:i A') }}">
            </div>
            <div class="col-12 mb-3">
                <label>Description</label><i class="text-danger"> *</i>
                <textarea class="form-control" row="5" readonly>{{ $file->description ?? ''}}</textarea>
            </div>

            <div class="col-12 mb-3 mt-3">
                <h4>File Items</h4>
                <table class="table table-bordered text-white">
                    <thead class=" text-uppercase text-bolder bg-secondary"><tr><td>Filename</td><td>Action</td></tr></thead>
                    <tbody>
                        @foreach($file->items as $item)
                            <tr>
                                <td class="text-primary">{{ $item->file_name ?? ''}}</td>
                                <td><a href="{{ route('archives-download-file', $item->id) }}" target="_blank" class="btn btn-success"><i class="fa fa-eye"></i> View</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    @include('archives.common.modals')
@endsection

@section('js')
    @include('archives.common.js')
@endsection