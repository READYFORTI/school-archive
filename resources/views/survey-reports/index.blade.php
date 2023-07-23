@extends('layout.sidebar')
@section('title')
<title>Survey Reports</title>
@endsection
@section('page')
    <div class="page-header">
        <h1>Survey Reports</h1>
    </div>
    <div class="container">
        @include('layout.alert')
        <div class="mb-4 row">
            <div class="row mt-4 col-12">
                @foreach($survey_reports as $report)
                    <div class="col-2 text-center">
                        <div class="btn align-items-center justify-content-center btn-directory" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Storage::url('assets/file-white.png') }}" alt="Folder.png" class="img-fluid">
                            <p class="text-white" style="text-overflow: ellipsis"><small>{{ $report->name ?? '' }}</small></p>
                            
                            <a href="#" class="btn btn-sm btn-success btn-confirm" data-message="Are you sure you want to approve?" data-target="#approve_report_{{ $report->id }}">Approve</button>
                                <form id="approve_report_{{ $report->id }}" action="{{ route(auth()->user()->role->role_name == 'College Management Team' ? 'cmt.survey-reports.approve' : 'admin-survey-reports.approve', $report->id) }}" class="d-none" method="POST">@csrf</form>
                            </a>
                            <a href="#" class="btn btn-sm btn-warning btn-confirm" data-message="Are you sure you want to reject?" data-target="#approve_report_{{ $report->id }}">Reject</button>
                                <form id="approve_report_{{ $report->id }}" action="{{ route(auth()->user()->role->role_name == 'College Management Team' ? 'cmt.survey-reports.reject' : 'admin-survey-reports.reject', $report->id) }}" class="d-none" method="POST">@csrf</form>
                            </a>
                        </div>
                        <ul class="dropdown-menu text-center">
                            <li><a href="{{ route('archives-show-file', $report->file_id) }}" target="_blank" class="text-decoration-none"><i class="fa fa-eye"></i> Open</a></li>
                            <li><a href="#" class="text-decoration-none btn-property"
                                data-bs-toggle="modal" data-bs-target="#propertyModal"
                                data-name="{{ $report->name }}"
                                data-created-by="{{ $report->user->username ?? 'Admin' }}"
                                data-created-at="{{ $report->created_at ? $report->created_at->format('M d, Y h:i A') : '' }}"
                                data-updated-at="{{ $report->created_at ? $report->created_at->format('M d, Y h:i A') : '' }}"
                                data-description="{{ $report->description ?? ''}}"
                            ><i class="fa fa-cog"></i> Properties</a></li>
                            <li><a href="#" class="text-decoration-none btn-remarks" data-bs-toggle="modal" data-bs-target="#remarksModal"
                                    data-file-id="{{ $report->file_id }}"
                                    data-route="{{ route('save-remarks', $report->file_id) }}">
                                        <i class="fa fa-envelope"></i> Remarks
                                </a>
                            </li>
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="modal fade" id="propertyModal" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Properties</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <table class="table">
                            <tr><td><strong>Name:</strong></td><td id="propertyName"></td></tr>
                            <tr><td><strong>Type:</strong></td><td id="propertyType"></td></tr>
                            <tr><td><strong>Created By:</strong></td><td id="propertyCreatedBy"></td></tr>
                            <tr><td><strong>Created:</strong></td><td id="propertyCreated"></td></tr>
                            <tr><td><strong>Updated:</strong></td><td id="propertyUpdated"></td></tr>
                            <tr><td colspan="2"><strong>Description:</strong></td></tr>
                            <tr>
                                <td colspan="2">
                                    <textarea class="form-control" row="5" readonly id="propertyDescription"></textarea>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Remarks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="remarksForm">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="remarksDetailForm">
                                <div class="col-12 mb-3">
                                    <label class="form-label">Choose Remarks:</label><br/>
                                    <input type="radio" class="btn-check" name="type" id="remarks-success" value="success" autocomplete="off" checked>
                                    <label class="btn btn-outline-success p-2 px-4" for="remarks-success"></label>

                                    <input type="radio" class="btn-check" name="type" id="remarks-danger" value="danger" autocomplete="off">
                                    <label class="btn btn-outline-danger p-2 px-4" for="remarks-danger"></label>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="comments">Comments:</label>
                                    <textarea class="form-control" rows="3" name="comments" id="remarks-comments"></textarea>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="comments">Recent Remarks:</label>
                                <table class="table recent-remarks-table"></table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-submit-remarks">Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>

    var files = {!! json_encode($files) !!};
    var user_id = parseInt("{{ Auth::user()->id }}");

    $('.btn-property').on('click', function(){
        $('#propertyName').html($(this).data('name'));
        $('#propertyType').html($(this).data('type'));
        $('#propertyCreatedBy').html($(this).data('created-by'));
        $('#propertyCreated').html($(this).data('created-at'));
        $('#propertyUpdated').html($(this).data('updated-at'));
        $('#propertyDescription').html($(this).data('description'));
    });

    $('.btn-remarks').on('click', function(){
        var file_id = parseInt($(this).data('file-id'));
        
        $('.btn-submit-remarks').hide();
        $('.remarksDetailForm').hide();

        if( $(this).data('route')) {
            $('#remarksForm').prop('action', $(this).data('route'));
            $('.btn-submit-remarks').show();
            $('.remarksDetailForm').show();
        }
        
        
        var file = files.find(item => 
            item.id === file_id
        );

        $('.recent-remarks-table').html('');
        if(file.remarks.length > 0) {
            var remark = file.remarks.find(item => item.user_id === user_id);
            if(remark) {
                $('#remarks-' + remark.type).prop('checked', true);
                $('#remarks-comments').html(remark.comments);
            }
            file.remarks.forEach(function(i){
                $('.recent-remarks-table').append(`
                    <tr>
                        <td class="text-center">
                            <i class="fa fa-user text-` + i.type + ` fa-2x"></i><br/>
                            <small class="badge bg-secondary" data-bs-toggle="tooltip" title="` + i.created_at_formatted + `">` + i.created_at_for_humans + `</small>
                        </td>
                        <td><strong class="px-0">` + i.user.firstname + ` ` + i.user.surname + `</strong><br/>` +
                            `(` + i.user.role.role_name + `)<br/>` + 
                            i.comments + `
                        </td>
                    </tr>
                `);
            });
        }
    });
</script>
@endsection