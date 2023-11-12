@extends('layout.sidebar')
@section('title')
<title>{{ Archives }}</title>
@endsection
@section('page')
    <div class="page-header">
        <h1>{{ Archives }}</h1>
        <h5 class="text-decoration-none">
            <a href="{{ route('po.archives') }}">Archives</a>
        </h5>
    </div>
    <div class="container">
        <div class="mb-4 row">
            @foreach($directories as $directory)
                <div class="col-2 text-center">
                    <button class="btn align-items-center justify-content-center btn-directory" data-bs-toggle="dropdown" aria-expanded="false" data-route="{{ route($route ?? 'archives-page') }}?directory={{ $directory->id }}&user={{ $current_user->id }}">
                        <img src="{{ Storage::url('assets/folder-green.png') }}" alt="Folder.png" class="img-fluid">
                        <p class="text-black" style="text-overflow: ellipsis"><small>{{ $directory->name ?? '' }}</small></p>
                    </button>
                    <ul class="dropdown-menu text-center">
                        <li><a href="{{ route($route ?? 'archives-page') }}?directory={{ $directory->id }}&user={{ $current_user->id }}" class="text-decoration-none">Open Directory</a></li>
                        <li><a href="#" class="text-decoration-none btn-property"
                            data-bs-toggle="modal" data-bs-target="#propertyModal"
                            data-name="{{ $directory->name }}"
                            data-type="Folder"
                            data-created-by="{{ $directory->user->username ?? 'Admin' }}"
                            data-created-at="{{ $directory->created_at ? $directory->created_at->format('M d, Y h:i A') : '' }}"
                            data-updated-at="{{ $directory->created_at ? $directory->created_at->format('M d, Y h:i A') : '' }}"
                        >Properties</a></li>
                        
                        @if(Auth::user()->role->role_name == 'Document Control Custodian' && !empty($current_directory->area) && $current_directory->area->type == 'process')
                        <li>
                            <a href="#" class="text-decoration-none toggleDirectoryModal"
                                data-name="{{ $directory->name }}" 
                                data-route="{{ route('archives-update-directory', $directory->id) }}" 
                                data-bs-toggle="modal" data-bs-target="#directoryModal">
                                    Rename
                            </a>
                        </li>
                        <!-- <li>
                            <a href="#" class="text-decoration-none btn-confirm" data-target="#delete_directory_{{ $directory->id }}">Delete</button>
                                <form id="delete_directory_{{ $directory->id }}" action="{{ route('archives-delete-directory', $directory->id) }}" class="d-none" method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </a>
                        </li> -->
                        @endif
                    </ul>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('archives-search') }}" id="searchModalForm">
                    @csrf
                    <input type="hidden" value="{{ $current_search->id ?? '' }}" name="parent_search">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="fileSearch" class="form-label">File Name</label>
                            <input type="text" class="form-control" name="fileSearch" id="fileSearch" placeholder="Enter File Name" required>
                        </div>
                        @if(!empty($users) && in_array(Auth::user()->role->role_name, Config::get('app.manage_archive')))
                            <div class="mb-3">
                                <label for="search" class="form-label">User</label>
                                <select class="form-control" name="userSearch">
                                    <option value="">All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ sprintf("%s %s - %s", $user->firstname ?? '', $user->surname ?? '', $user->role->role_name ?? '') }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    $('.btn-directory').on('dblclick', function(){
        location.href = $(this).data('route')
    });
</script>
@endsection