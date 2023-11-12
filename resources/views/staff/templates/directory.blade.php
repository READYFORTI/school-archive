@extends('layout.sidebar')
@section('title')
    <title>Evidence</title>
@endsection
@section('page')
    <style>
        .folder{
            width: 140px;
            height: 123px;
            background-repeat: no-repeat;
            background-position: center;
            background-image: url("{{ Storage::url('assets/folder.png') }}");
            color: #ffffff;
        }
        .folder:hover{
            border: 2px #d1d1d1 solid;
            color: #ffffff;
        }
        .folder:focus{
            border: 2px #d1d1d1 solid !important;
            color: #ffffff !important;
        }
        .folder:active{
            border: 2px #d1d1d1 solid !important;
            color: #ffffff !important;
        }
        .file{
            width: 140px;
            height: 123px;
            background-repeat: no-repeat;
            background-position: center;
            color: #ffffff;
        }
        .file:hover{
            border: 2px #d1d1d1 solid;
            color: #ffffff;
        }
        .file:focus{
            border: 2px #d1d1d1 solid !important;
            color: #ffffff !important;
        }
        .file:active{
            border: 2px #d1d1d1 solid !important;
            color: #ffffff !important;
        }
        .dropdown-menu li:not(:last-child) .dropdown-item:hover{
            background-color: #498f49 !important;
            color: #ffffff !important;
        }
        .dropdown-menu li:last-child .dropdown-item:hover {
            background-color: #fa0303 !important;
            color: #ffffff !important;
        }
    </style>
    <div class="container">
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dcc-show-evidence') }}">Area</a></li>
                @if (Route::current()->parameter('office'))
                <li class="breadcrumb-item">
                    <a href="{{ route('dcc-show-office-process',request()->route('office')) }}">{{ request()->route('office') }}</a>
                </li>
                @else
                <li class="breadcrumb-item">
                    <a href="{{ route('dcc-show-program-process',request()->route('program')) }}">{{ request()->route('program') }}</a>
                </li>
                @endif
                @if (!isset($folders) || count($folders) == 0)
                <li class="breadcrumb-item active" aria-current="page">
                    {{ $process }}
                </li>
                @else
                    @if(Route::getCurrentRoute()->getName() == 'dcc-show-evidence-directory-program-parent')
                        <li class="breadcrumb-item">
                            <a href="{{ route('dcc-show-evidence-directory-program',[request()->route('program'),request()->route('process')]) }}">{{ $process }}</a>
                        </li>
                        @foreach ($folders as $key => $item)
                            @if ($loop->last)
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $item }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dcc-show-evidence-directory-program',[request()->route('program'),request()->route('process')]).'/'.$key }}">{{ $item }}</a>
                                </li>
                            @endif
                        @endforeach
                    @else
                        <li class="breadcrumb-item">
                            <a href="{{ route('dcc-show-evidence-directory-office',[request()->route('office'),request()->route('process')]) }}">{{ $process }}</a>
                        </li>
                        @foreach ($folders as $key => $item)
                            @if ($loop->last)
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $item }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ route('dcc-show-evidence-directory-office',[request()->route('office'),request()->route('process')]).'/'.$key }}">{{ $item }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                @endif
                
            </ol>
        </nav>
    </div>

    <div class="container">
        <div class="text-end">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add">
                <span class="mdi mdi-plus"></span> Add
            </button>
        </div>
    </div>
    {{-- Transaction Messages --}}
    <div class="container">
        @if (session('success'))
            <div class="mt-3 alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-3 alert alert-danger alert-dismissible fade show">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
            
        @if (session('err'))
        <div class="mt-3 alert alert-danger alert-dismissible fade show">
            {{ session('err') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        {{-- List --}}
        <div class="row">
            @if (Route::getCurrentRoute()->getName() == 'dcc-show-evidence-directory-office' || Route::getCurrentRoute()->getName() == 'dcc-show-evidence-directory-program')
            @foreach ($evidence as $item)
            <div class="col-2">
                {{-- {{ $item }} --}}
                <div class="dropwdown">
                    @if (Route::getCurrentRoute()->getName() == 'dcc-show-evidence-directory-program')
                        @if (is_null($item->directory_id))
                        <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route(Route::getCurrentRoute()->getName(),[request()->route('program'),request()->route('process')]) . '/' . $item->id}}">
                            {{ $item->folder_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#edit{{$item->id}}">Rename</button></li>
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#remove{{$item->id}}">Remove</button></li>
                        </ul>
                        @else
                        <button class="file btn d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false">
                            <div>
                                <img src="{{ Storage::url('assets/file.png') }}" alt="File.png" class="img-fluid w-75">
                                <span class="text-white">{{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}</span>
                            </div>
                        </button>
                        <div class="mt-2 text-center" style="width: 140px;">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#remark{{$item->id}}">
                                Remark
                            </button>
                        </div>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view_details{{$item->id}}">View Details</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#revision_history{{$item->id}}">Revision History</button></li>
                            <li><a class="dropdown-item" id="download" href="{{route('download-evidence',[$item->id])}}">Download</a></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#download_history{{$item->id}}">Download History</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_file{{$item->id}}">Delete</button></li>
                        </ul>
                        @endif
                    @else
                        @if (is_null($item->directory_id))
                        <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route(Route::getCurrentRoute()->getName(),[request()->route('office'),request()->route('process')]) . '/' . $item->id}}">
                            {{ $item->folder_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#edit{{$item->id}}">Rename</button></li>
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#remove{{$item->id}}">Remove</button></li>
                        </ul>
                        @else
                        <button class="file btn d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false">
                            <div>
                                <img src="{{ Storage::url('assets/file.png') }}" alt="File.png" class="img-fluid w-75">
                                <span class="text-white">{{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}</span>
                            </div>
                        </button>
                        <div class="mt-2 text-center" style="width: 140px;">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#remark{{$item->id}}">
                                Remark
                            </button>
                        </div>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view_details{{$item->id}}">View Details</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#revision_history{{$item->id}}">Revision History</button></li>
                            <li><a class="dropdown-item" id="download" href="{{route('download-evidence',[$item->id])}}">Download</a></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#download_history{{$item->id}}">Download History</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_file{{$item->id}}">Delete</button></li>
                        </ul>
                        @endif
                    @endif
                    
                </div>
                <div class="modal fade" id="edit{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Rename Folder Evidence</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('dcc-rename-folder-evidence') }}" method="POST">
                                    @csrf
                                    {{-- <input type="hidden" id="whitelist" value='{!! $programs !!}'> --}}
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <div class="mb-2">
                                        <span>Folder Name</span>
                                        <input type="text" class="form-control" placeholder="Folder" name="folder" value="{{ $item->folder_name }}" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">Save</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="remove{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Remove Folder Evidence</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('dcc-remove-folder-evidence') }}" method="POST">
                                    @csrf
                                    {{-- <input type="hidden" id="whitelist" value='{!! $programs !!}'> --}}
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <div class="mb-2">
                                        <h3 class="text-center">Are you sure you want to remove {{ $item->folder_name }} folder?</h3>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-danger">Remove</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if (!is_null($item->directory_id))
                <div class="modal fade" id="view_details{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">File Details</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <span>Filename:</span>
                                        <p class="text-center">{{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}</p>
                                    </div>
                                    <div class="col-12">
                                        <span>Submitted by:</span>
                                        <p class="text-center">{{ $item['user']['firstname'] }} {{ $item['user']['surname'] }}</p>
                                    </div>
                                    <div class="col-12">
                                        <span>Uploaded on:</span>
                                        <p class="text-center">{{ date('F j, Y', strtotime($item['created_at'])) }}</p>
                                    </div>
                                    <div class="col-12">
                                        <span>Assigned:</span>
                                        <p class="text-center">{{ $item['process']['process_name'] }}</p>
                                    </div>
                                    @if (count($item['evidence_remarks']) > 0)
                                    <div class="col-12">
                                        <span>Recent Remarks</span>
                                        @foreach ($item['evidence_remarks'] as $remark)
                                        <p class="text-center">
                                            {{ $remark->user->firstname }} {{ $remark->user->surname }} 
                                            <button disabled="disabled" class="btn {{ $remark->status == 'good' ? 'btn-success': ($remark->status == 'bad' ? 'btn-danger' : 'btn-warning') }}">
                                            {{ $remark->comment }}
                                            </button>
                                        </p>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="revision_history{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Revision History</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    @foreach ($item['evidence_histories'] as $history)
                                    <div class="col-12 text-center">
                                        <a target="_blank" href="{{ Storage::url($history['location']) }}" class="btn btn-link text-center">{{$history['filename']}}.{{$history['extension']}} - {{ date('F j, Y h:mA', strtotime($item['updated_at'])) }}</a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="download_history{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Download History</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    @foreach ($item['evidence_downloads'] as $download)
                                    <div class="col-12 text-center">
                                        <p class="text-center">{{ $download['user']['firstname'] }} {{ $download['user']['surname'] }} - {{ date('F j, Y', strtotime($download['created_at'])) }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="delete_file{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">File Details</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('delete-file-evidence')}}" method="post">
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    @csrf
                                    <div class="text-center">
                                        <h3>Are you sure you want to delete {{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}?</h3>
                                        <button type="submit" class="mt-3 btn btn-danger">Delete</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="remark{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Remark</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('add-remark')}}" method="post">
                                    <input type="hidden" name="evidence" value="{{ $item->id }}">
                                    @csrf
                                    <div class="mb-3">
                                        <span>Choose Remarks</span>
                                        <div class="row">
                                            <div class="col-4">
                                                <input type="radio" class="btn-check w-100" name="status" id="option1" required autocomplete="off" value="good">
                                                <label class="btn btn-outline-success w-100" for="option1">&nbsp;</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="radio" class="btn-check w-100" name="status" id="option2" required autocomplete="off" value="neutral">
                                                <label class="btn btn-outline-warning w-100" for="option2">&nbsp;</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="radio" class="btn-check w-100" name="status" id="option3" required autocomplete="off" value="bad">
                                                <label class="btn btn-outline-danger w-100" for="option3">&nbsp;</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <span>Comment</span>
                                        <input type="text" class="form-control" name="comment" placeholder="Remark" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
            </div>
            @endforeach
            @else
            @foreach ($evidence as $item)
            <div class="col-2">
                {{-- {{ $item }} --}}
                <div class="dropdown">
                    @if (Route::getCurrentRoute()->getName() == 'dcc-show-evidence-directory-program-parent')
                        @if (is_null($item->directory_id))
                        <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route(Route::getCurrentRoute()->getName(),[request()->route('program'),request()->route('process'),$item->id])}}">
                            {{ $item->folder_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#edit{{$item->id}}">Rename</button></li>
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#remove{{$item->id}}">Remove</button></li>
                        </ul>
                        @else
                        <button class="file btn d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false">
                            <div>
                                <img src="{{ Storage::url('assets/file.png') }}" alt="File.png" class="img-fluid w-75">
                                <span class="text-white">{{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}</span>
                            </div>
                        </button>
                        <div class="mt-2 text-center" style="width: 140px;">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#remark{{$item->id}}">
                                Remark
                            </button>
                        </div>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view_details{{$item->id}}">View Details</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#revision_history{{$item->id}}">Revision History</button></li>
                            <li><a class="dropdown-item" id="download" href="{{route('download-evidence',[$item->id])}}">Download</a></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#download_history{{$item->id}}">Download History</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_file{{$item->id}}">Delete</button></li>
                        </ul>
                        @endif
                    @else
                        @if (is_null($item->directory_id))
                        <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route(Route::getCurrentRoute()->getName(),[request()->route('office'),request()->route('process'),$item->id])}}">
                            {{ $item->folder_name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#edit{{$item->id}}">Rename</button></li>
                            <li><button class="dropdown-item fold" data-bs-toggle="modal" data-bs-target="#remove{{$item->id}}">Remove</button></li>
                        </ul>
                        @else
                        <button class="file btn d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false">
                            <div>
                                <img src="{{ Storage::url('assets/file.png') }}" alt="File.png" class="img-fluid w-75">
                                <span class="text-white">{{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}</span>
                            </div>
                        </button>
                        <div class="mt-2 text-center" style="width: 140px;">
                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#remark{{$item->id}}">
                                Remark
                            </button>
                        </div>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view_details{{$item->id}}">View Details</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#revision_history{{$item->id}}">Revision History</button></li>
                            <li><a class="dropdown-item" id="download" href="{{route('download-evidence',[$item->id])}}">Download</a></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#download_history{{$item->id}}">Download History</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_file{{$item->id}}">Delete</button></li>
                        </ul>
                        @endif
                    @endif
                    
                </div>
                <div class="modal fade" id="edit{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Rename Folder Evidence</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('dcc-rename-folder-evidence') }}" method="POST">
                                    @csrf
                                    {{-- <input type="hidden" id="whitelist" value='{!! $programs !!}'> --}}
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <div class="mb-2">
                                        <span>Folder Name</span>
                                        <input type="text" class="form-control" placeholder="Folder" name="folder" value="{{ $item->folder_name }}" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">Save</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="remove{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Remove Folder Evidence</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ route('dcc-remove-folder-evidence') }}" method="POST">
                                    @csrf
                                    {{-- <input type="hidden" id="whitelist" value='{!! $programs !!}'> --}}
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    <div class="mb-2">
                                        <h3 class="text-center">Are you sure you want to remove {{ $item->folder_name }} folder?</h3>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-danger">Remove</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @if (!is_null($item->directory_id))
                <div class="modal fade" id="view_details{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">File Details</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <span>Filename:</span>
                                        <p class="text-center">{{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}</p>
                                    </div>
                                    <div class="col-12">
                                        <span>Submitted by:</span>
                                        <p class="text-center">{{ $item['user']['firstname'] }} {{ $item['user']['surname'] }}</p>
                                    </div>
                                    <div class="col-12">
                                        <span>Uploaded on:</span>
                                        <p class="text-center">{{ date('F j, Y', strtotime($item['created_at'])) }}</p>
                                    </div>
                                    <div class="col-12">
                                        <span>Assigned:</span>
                                        <p class="text-center">{{ $item['process']['process_name'] }}</p>
                                    </div>
                                    @if (count($item['evidence_remarks']) > 0)
                                    <div class="col-12">
                                        <span>Recent Remarks</span>
                                        @foreach ($item['evidence_remarks'] as $remark)
                                        <p class="text-center">
                                            {{ $remark->user->firstname }} {{ $remark->user->surname }} 
                                            <button disabled="disabled" class="btn {{ $remark->status == 'good' ? 'btn-success': ($remark->status == 'bad' ? 'btn-danger' : 'btn-warning') }}">
                                            {{ $remark->comment }}
                                            </button>
                                        </p>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="revision_history{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Revision History</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    @foreach ($item['evidence_histories'] as $history)
                                    <div class="col-12 text-center">
                                        <a target="_blank" href="{{ Storage::url($history['location']) }}" class="btn btn-link text-center">{{$history['filename']}}.{{$history['extension']}} - {{ date('F j, Y h:mA', strtotime($item['updated_at'])) }}</a>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="download_history{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Download History</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    @foreach ($item['evidence_downloads'] as $download)
                                    <div class="col-12 text-center">
                                        <p class="text-center">{{ $download['user']['firstname'] }} {{ $download['user']['surname'] }} - {{ date('F j, Y', strtotime($download['created_at'])) }}</p>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="delete_file{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">File Details</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('delete-file-evidence')}}" method="post">
                                    <input type="hidden" name="id" value="{{ $item->id }}">
                                    @csrf
                                    <div class="text-center">
                                        <h3>Are you sure you want to delete {{ $item['directory']['filename'] }}.{{ $item['directory']['extension'] }}?</h3>
                                        <button type="submit" class="mt-3 btn btn-danger">Delete</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal fade" id="remark{{$item->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Remark</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('add-remark')}}" method="post">
                                    <input type="hidden" name="evidence" value="{{ $item->id }}">
                                    @csrf
                                    <div class="mb-3">
                                        <span>Choose Remarks</span>
                                        <div class="row">
                                            <div class="col-4">
                                                <input type="radio" class="btn-check w-100" name="status" id="option1" required autocomplete="off" value="good">
                                                <label class="btn btn-outline-success w-100" for="option1">&nbsp;</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="radio" class="btn-check w-100" name="status" id="option2" required autocomplete="off" value="neutral">
                                                <label class="btn btn-outline-warning w-100" for="option2">&nbsp;</label>
                                            </div>
                                            <div class="col-4">
                                                <input type="radio" class="btn-check w-100" name="status" id="option3" required autocomplete="off" value="bad">
                                                <label class="btn btn-outline-danger w-100" for="option3">&nbsp;</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <span>Comment</span>
                                        <input type="text" class="form-control" name="comment" placeholder="Remark" required>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
            @endif
            
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add Folder Evidence</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('dcc-add-folder-evidence') }}" id="area" method="POST">
                        @csrf
                        {{-- <input type="hidden" id="whitelist" value='{!! $programs !!}'> --}}
                        <input type="hidden" name="parent" value="{{ Route::current()->parameter('parent') }}">
                        <input type="hidden" name="process" value="{{ Route::current()->parameter('process') }}">
                        <div class="mb-2">
                            <span>Folder Name</span>
                            <input type="text" class="form-control" placeholder="Folder" name="folder" required>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        const data = {!! json_encode($evidence) !!};
        console.log(data);

        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('contextmenu','.folder', function (event) {
                event.preventDefault();
                $(this).siblings('.dropdown-menu').toggle();
                // $('.dropdown-menu').removeClass('show');
                // $(this).next().addClass('show');
            });
            $('.dropdown-item .fold').on('click', function() {
                $(this).parent().parent().toggle();
            });
            $('#download').on('click', function () {
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        });
    </script>
@endsection
