@extends('layout.sidebar')
@section('title')
<title>{{ $page_title ?? 'Archives' }}</title>
@endsection
@section('page')
<div class="page-header">
    <h1>{{ $page_title ?? 'Archives' }}</h1>
    <h5 class="text-decoration-none">
        @if(empty($page_title) || $page_title == 'Archives')
            @if(!empty($parents))
                <a href="{{ route('archives-page') }}">Archives</a> >
            @endif
        @endif
        @if(!empty($parents))
            @foreach($parents as $parent) 
                @if(!$loop->last)
                    <a href="{{ route($route ?? 'archives-page') }}?directory={{ $parent['id'] }}">{{ $parent['name'] }}</a> >
                @else
                    {{ $parent['name'] }}
                @endif
            @endforeach
        @endif
    </h5>
</div>
    {{-- <div class="container"> --}}
    <div class="m-3">
        <div class="mb-3" style="text-align:right">
            @if(Auth::user()->role->role_name == Roles::PROCESS_OWNER 
                && $page_title == 'Manuals' 
                && !empty($current_directory->area) && $current_directory->area->type == 'process'
            )   
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#manualsModal"><i class="fa fa-book"></i> Upload Manual</button>
            @endif
            @if (Auth::user()->role->role_name == Roles::STAFF
                && $page_title == 'Manuals' 
                && !empty($current_directory->area) && $current_directory->area->type == 'process'
            )
                <button class="btn btn-success" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-plus"></i> New</button>
                <ul class="dropdown-menu text-left">
                    <li>
                        <button class="btn toggleDirectoryModal"
                            data-route="{{ route('archives-store-directory') }}" 
                            data-bs-toggle="modal" data-bs-target="#directoryModal">
                                Folder
                        </button>
                    </li>
                </ul>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#manualsModal"><i class="fa fa-book"></i> Upload Manual</button>
            @endif
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#searchModal"><i class="fa fa-search"></i> Search</button>
            @if(
                    (in_array(Auth::user()->role->role_name, [Roles::DOCUMENT_CONTROL_CUSTODIAN, Roles::PROCESS_OWNER])
                        && !empty($current_directory)
                        && in_array($page_title, ['Evidences', 'Manuals'])
                        && (!empty($current_directory->area) && $current_directory->area->type == 'process')
                    )
                ||  (Auth::user()->role->role_name == Roles::STAFF 
                        && !empty($current_directory) 
                        && $page_title == 'Templates'
                    )
                )
                <button class="btn btn-success" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-plus"></i> New</button>
                <ul class="dropdown-menu text-left">
                    <li>
                        <button class="btn toggleDirectoryModal"
                            data-route="{{ route('archives-store-directory') }}" 
                            data-bs-toggle="modal" data-bs-target="#directoryModal">
                                Folder
                        </button>
                    </li>
                    @if(Auth::user()->role->role_name == Roles::STAFF)
                    <li>
                        <button class="btn"
                            data-bs-toggle="modal" data-bs-target="#templateModal">
                                Template
                        </button>
                    </li>
                    @endif
                </ul>
            @endif
        </div>
        @include('layout.alert')
        <!-- @if(!empty($users) && in_array(Auth::user()->role->role_name, Config::get('app.manage_archive')))
            <h5>User:</h5>
            <select class="form-control userSelection">
                <option value="">All Users</option>
                @php $roles = $users->groupBy('role.role_name'); @endphp
                @foreach($roles as $role => $users)
                    <optgroup label="{{ $role }}">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ $current_user->id == $user->id ? 'selected' : ''}}>{{ sprintf("%s %s", $user->firstname ?? '', $user->surname ?? '') }}</option>
                    @endforeach
                    </optgroup>
                @endforeach
            </select>
        @endif -->
        @if(!empty($directories))
            <div class="mb-4 row">
                @foreach($directories as $directory)
                   @include('archives.common.directory')
                @endforeach
            </div>
        @endif

        @if(!empty($files))
            <div class="mt-3 row">
                @foreach($files as $file)
                   @include('archives.common.file')
                @endforeach
            </div>
        @endif
    </div>


    <div class="modal fade" id="directoryModal" tabindex="-1" aria-labelledby="directoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="directoryModalLabel">Add Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('archives-store-directory') }}" id="directoryModalForm">
                    @csrf
                    <input type="hidden" value="{{ $current_directory->id ?? '' }}" name="parent_directory">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="directory" class="form-label">Name</label>
                            <input type="text" class="form-control" name="directory" id="directory" placeholder="Enter Folder Name" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="fileModal" tabindex="-1" aria-labelledby="fileModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="fileModalLabel">Upload File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="{{ route('archives-store-file') }}" enctype="multipart/form-data" id="fileModalForm">
                    @csrf
                    <input type="hidden" value="{{ $current_directory->id ?? '' }}" name="parent_directory">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="file_name" class="form-label">Name</label>
                            <input type="text" class="form-control" name="file_name" id="file_name" placeholder="Enter File" required>
                        </div>
                        <div class="mb-3">
                            <label for="file_attachments" class="form-label">Attachments</label>
                            <input type="file" class="form-control" name="file_attachments[]" id="file_attachments" 
                                required multiple accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save changes</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchModalLabel">Search File or Folder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="GET" action="{{ route('search', strtolower($page_title ?? 'Archives')) }}" id="searchModalForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="keyword" class="form-label">File or Folder Name</label>
                            <input type="text" class="form-control" name="keyword" id="keyword" placeholder="Enter File Name" required>
                        </div>
                        <div class="accordion" id="dateFilterAccordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button type="button" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#searchCollapse">Search Date</button>
                                </h2>
                                <div id="searchCollapse" class="accordion-collapse collapse m-2" data-bs-parent="#dateFilterAccordion">
                                    <div class="mb-3">
                                        <label for="keyword" class="form-label">Date From</label>
                                        <div class="input-group mb-3">
                                            <input type="date" name="date_from" class="date-from form-control" id="date_from">
                                            <div class="input-group-append">
                                                <button type="button" class="input-group btn btn-warning btn-clear-date" data-target="#date_from">Clear</span>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="mb-3">
                                        <label for="keyword" class="form-label">Date To</label>
                                        <div class="input-group mb-3">
                                            <input type="date" name="date_to" class="date-to form-control" id="date_to">
                                            <div class="input-group-append">
                                                <button type="button" class="input-group btn btn-warning btn-clear-date" data-target="#date_to">Clear</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Search</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @if(Auth::user()->role->role_name == Roles::STAFF && !empty($current_directory) && $page_title == 'Templates')
        <div class="modal fade" id="templateModal" tabindex="-1" aria-labelledby="templateModalModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="templateModalModalLabel">Upload Template</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('staff.template.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="current_directory" name="current_directory" value="{{ $current_directory->id ?? '' }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Filename" required>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date:</label>
                                <input type="date" id="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"/>
                            </div>
                            <div class="mb-3">
                                <label for="file_attachments" class="form-label">Attachment</label>
                                <input type="file" class="form-control" 
                                    name="file_attachments[]" id="file_attachments" 
                                    required multiple accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                            </div>
                            <div class="mb-3">
                                <label for="search" class="form-label">Description:</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if((Auth::user()->role->role_name == Roles::PROCESS_OWNER || Auth::user()->role->role_name == Roles::STAFF) && $page_title == 'Manuals')
        <div class="modal fade" id="manualsModal" tabindex="-1" aria-labelledby="manualsModalModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="manualsModalModalLabel">Upload Manuals</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route(Auth::user()->role->role_name == Roles::STAFF?'staff.manual.store':'po.manual.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="current_directory" name="directory" value="{{ $current_directory->id ?? '' }}">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="name" placeholder="Enter Filename" required>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Date:</label>
                                <input type="date" id="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"/>
                            </div>
                            <div class="mb-3">
                                <label for="file_attachments" class="form-label">Attachment</label>
                                <input type="file" class="form-control" 
                                    name="file_attachments[]" id="file_attachments" 
                                    required multiple accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                            </div>
                            <div class="mb-3">
                                <label for="search" class="form-label">Description:</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Save changes</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    @include('archives.common.modals')
@endsection

@section('js')
    @include('archives.common.js')
    <script>
        var dateFrom = $('.date-from').flatpickr({
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            maxDate: "{{ date('Y-m-d') }}"
        });

        
        var dateTo = $('.date-to').flatpickr({
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            maxDate: "{{ date('Y-m-d') }}"
        });
    </script>
@endsection