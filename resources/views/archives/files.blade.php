@extends('layout.sidebar')
@section('title')
<title>{{ $page_title }}</title>
@endsection
@section('page')
    <div class="page-header d-none">
        <h1>{{ $page_title ?? 'Archives' }}</h1>
        {{-- <h5>{{ !empty($parent_directory->name) ? $parent_directory->name.' > ' : '' }}{{ $directory->name ?? '' }}</h5> --}}
    </div>
    <div class="m-3">
        @include('layout.alert')
        
        <div class="mb-4 row">
            @foreach($directories as $directory)
                <div class="col-2 text-center">
                    <button class="btn align-items-center justify-content-center btn-directory" data-bs-toggle="dropdown" aria-expanded="false" data-route="{{ route($route ?? 'archives-page') }}?directory={{ $directory->id }}">
                        <img src="{{ Storage::url('assets/folder-green.png') }}" alt="Folder.png" class="img-fluid">
                        <p class="text-white" style="text-overflow: ellipsis"><small>
                            @if(in_array($current_user->role->role_name, ['Process Owner', 'Internal Auditor']))
                                {{ sprintf('%s%s%s', $directory->parent->parent->name ? $directory->parent->parent->name.' > ' : '', $directory->parent->name ? $directory->parent->name.' > ' : '', $directory->name ?? '') }}
                            @else
                                {{ $directory->name ?? '' }}
                            @endif
                        </small></p>
                    </button>
                    <ul class="dropdown-menu text-center">
                        <li><a href="{{ route($route ?? 'archives-page') }}?directory={{ $directory->id }}&user={{ $current_user->id }}" class="text-decoration-none">Open Folder</a></li>
                        <li><a href="#" class="text-decoration-none btn-property"
                            data-bs-toggle="modal" data-bs-target="#pro
                            pertyModal"
                            data-name="{{ $directory->name }}"
                            data-type="Folder"
                            data-created-by="{{ $directory->user->username ?? 'Admin' }}"
                            data-created-at="{{ $directory->created_at ? $directory->created_at->format('M d, Y h:i A') : '' }}"
                            data-updated-at="{{ $directory->created_at ? $directory->created_at->format('M d, Y h:i A') : '' }}"
                        >Properties</a></li>
                    </ul>
                </div>
            @endforeach
        </div>

        <div class="mt-3 row">
            @foreach($files as $file)
                <div class="col-2 text-center">
                    <button class="btn align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false" data-route="{{ route('archives-page') }}?directory={{ $file->id }}">
                        <img src="{{ Storage::url('assets/file.png') }}" alt="file.png" class="img-fluid">
                        <p class="text-white" style="text-overflow: ellipsis"><small>{{ $file->file_name ?? '' }}</small></p>
                    </button>
                    <ul class="dropdown-menu text-left px-3">
                        <li><a href="{{ route('archives-download-file', $file->id) }}" class="text-decoration-none"><i class="fa fa-download"></i> Download</a></li>
                        <li>
                            <a href="#" class="text-decoration-none btn-property"
                                data-bs-toggle="modal" data-bs-target="#propertyModal"
                                data-name="{{ $file->file_name }}"
                                data-type="{{ $file->file_mime }}"
                                data-created-by="{{ $file->user->username }}"
                                data-created-at="{{ $file->created_at->format('M d, Y h:i A') }}"
                                data-updated-at="{{ $file->created_at->format('M d, Y h:i A') }}"
                                data-description="{{ $file->description ?? ''}}"
                            ><i class="fa fa-cog"></i> Properties</a>
                        </li>
                        <!-- @if($file->user_id == Auth::user()->id)
                        <li>
                            <a href="#" class="text-decoration-none btn-share" data-bs-toggle="modal" data-bs-target="#shareModal" data-users="{{ $file->shared_users }}" data-route="{{ route('archives-share-file', $file->id) }}"><i class="fa fa-share"></i> Share</button>
                                <form id="unshare_file_{{ $file->id }}" action="{{ route('archives-unshare-file', $file->id) }}" class="d-none" method="POST">
                                    @csrf
                                </form>
                            </a>
                        </li>
                        @endif -->
                        @if($file->user_id == Auth::user()->id)
                        <li>
                            <a href="#" class="text-decoration-none btn-edit-file"
                                data-bs-toggle="modal" data-bs-target="#editFileModal"
                                data-route="{{ route('archives-update-file', $file->id) }}"
                                data-name="{{ $file->file_name }}"
                                data-description="{{ $file->description ?? ''}}"
                            ><i class="fa fa-edit"></i> Edit</a>
                        </li>
                        @endif
                        @if($file->user_id == Auth::user()->id || in_array(Auth::user()->role->role_name, Config::get('app.manage_archive')))
                        <li>
                            <a href="#" class="text-decoration-none btn-confirm" data-target="#delete_file_{{ $file->id }}"><i class="fa fa-trash"></i>Delete</button>
                                <form id="delete_file_{{ $file->id }}" action="{{ route('archives-delete-file', $file->id) }}" class="d-none" method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </a>
                        </li>
                        @endif
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

                        <h5>File History</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <td>Name</td>
                                    <td>Description</td>
                                    <td>Date</td>
                                    <td>File</td>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="#" id="updateFileModalForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="file_name" id="file_name" placeholder="Enter Audit Report Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Description</label>
                            <textarea class="form-control" name="file_description" id="file_description" placeholder="Enter File Description" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="file_attachments" class="form-label">Attachment</label>
                            <input type="file" class="form-control" name="file_attachments[]" id="file_attachments" 
                                required multiple
                                accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Share File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="#" id="shareModalForm">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="search" class="form-label">Share File With:</label>
                            <select class="form-control" name="userShare[]" id="userShare" multiple>
                                @foreach($users as $user)
                                    @if($user->id !== $current_user->id)
                                        <option value="{{ $user->id }}">{{ sprintf("%s %s - %s", $user->firstname ?? '', $user->surname ?? '', $user->role->role_name ?? '') }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-share"></i> Share</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    var userShare = $('#userShare').select2({
        dropdownParent: $('#shareModal'),
        width: '100%'
    });

    $('.toggleDirectoryModal').on('click', function(){
        $('#directoryModalForm').attr('action', $(this).data('route'));
        $('#directory').val($(this).data('name'));
    });

    $('.btn-confirm').on('click', function(){
        var form = $(this).data('target');
        var message = $(this).data('message') ?? "Are you sure you wan't to delete?";
        Swal.fire({
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(form).submit();
                }
        });
    });

    $('.userSelection').on('change', function(){
        var userID = $(this).val();
        if(userID == '') {
            location.href = "{{ route('archives-page') }}";
        }else{
            location.href = "{{ route('archives-page') }}?user=" + userID;
        }
    });

    $('.btn-property').on('click', function(){
        $('#propertyName').html($(this).data('name'));
        $('#propertyType').html($(this).data('type'));
        $('#propertyCreatedBy').html($(this).data('created-by'));
        $('#propertyCreated').html($(this).data('created-at'));
        $('#propertyUpdated').html($(this).data('updated-at'));
        $('#propertyDescription').html($(this).data('description'));
    });

    $('.btn-edit-file').on('click', function(){
        $('#updateFileModalForm').prop('action', $(this).data('route'));
        $('#file_name').val($(this).data('name'));
        $('#file_description').html($(this).data('description'));
    });

    $('.btn-share').on('click', function(){
        var users = "" + $(this).data('users') + "";
        $('#shareModalForm').prop('action', $(this).data('route'));
        $("#userShare option:selected").removeAttr("selected");

        if(users != '') {
            users = users.includes(', ') ? users.split(', ') : [users];
            
            userShare.val(users).trigger('change');
        }
    });

    $('.btn-directory').on('dblclick', function(){
        location.href = $(this).data('route')
    });
</script>
@endsection