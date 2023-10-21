@extends('layout.sidebar')
@section('title')
<title>Rejected Survey Reports</title>
@endsection
@section('page')
    <div class="page-header">
        <h2>Rejected Survey Reports</h2>
    </div>
    
        @include('layout.alert')
        <div class="m-3 row">
            <div class="row mt-2 col-8">
                @foreach($survey_reports as $report)
                    <div class="col-2" >
                        <div class="btn text-center align-items-center justify-content-center btn-directory" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ Storage::url('assets/file-red.png') }}" alt="Folder.png" class="img-fluid">
                            <p class="text-black" style="text-overflow: ellipsis"><small>{{ $report->name ?? '' }}</small></p>
                        </div>
                        <ul class="dropdown-menu px-2">
                            <li><a href="{{ route('archives-show-file', $report->file_id) }}" target="_blank" class="text-decoration-none"><i class="fa fa-eye"></i> Open</a></li>
                            <li><a href="#" class="text-decoration-none btn-property"
                                data-bs-toggle="modal" data-bs-target="#propertyModal"
                                data-name="{{ $report->name }}"
                                data-created-by="{{ $report->user->username ?? 'Admin' }}"
                                data-created-at="{{ $report->created_at ? $report->created_at->format('M d, Y h:i A') : '' }}"
                                data-updated-at="{{ $report->created_at ? $report->created_at->format('M d, Y h:i A') : '' }}"
                                data-description="{{ $report->description ?? ''}}"
                            ><i class="fa fa-cog"></i> Properties</a></li>
                        </ul>
                    </div>
                @endforeach
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
</script>
@endsection