@extends('layout.sidebar')
@section('title')
<title>Rejected Consolidated Audit Reports</title>
@endsection
@section('page')
    <div class="page-header">
        <h2>Rejected Consolidated Audit Reports</h2>
    </div>
   
        @include('layout.alert')
        <div class="m-3 row">
            <div class="row mt-4 col-8">
                @foreach($consolidated_audit_reports as $report)
                    <div class="col-2">
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