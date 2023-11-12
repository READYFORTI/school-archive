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
        background-image: url("{{ Storage::url('assets/folder-green.png') }}");
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
                <li class="breadcrumb-item"><a href="{{ route('po-evidence-page') }}">Root</a></li>
                <li class="breadcrumb-item"><a href="{{ route('po-program-process',[request()->route('program')]) }}">{{ $program }}</a></li>
                <li class="breadcrumb-item"><a href="{{ route('po-program-root-process',[request()->route('program'),request()->route('process')]) }}">{{ $process->process_name }}</a></li>
                @foreach ($navs as $key => $file)
                @if ($loop->last)
                    <li class="breadcrumb-item active" aria-current="page">
                        {{ $file }}
                    </li>
                @else
                    <li class="breadcrumb-item">
                        <a href="{{ route('po-program-parent-process',[request()->route('program'),request()->route('process'),$key]) }}">{{ $file }}</a>
                    </li>
                @endif
                @endforeach
            </ol>
        </nav>
        <div class="text-end">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#add">
                <span class="mdi mdi-plus"></span> Add
            </button>
        </div>
        @if (session('success'))
            <div class="mt-3 alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{-- Transactions --}}
        @if ($errors->any())
            <div class=" mt-3 alert alert-danger alert-dismissible fade show">
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

        <div class="row g-3">
            @if (count($folders) > 0)
            <div class="col-12">
                <h3>Folders</h3>
            </div>
            @endif
            @foreach ($folders as $folder)
                <div class="col-2">
                    <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route('po-program-parent-process',[request()->route('program'),request()->route('process'),$folder->id]) }}">
                        {{ $folder['folder_name'] }}
                    </a>
                </div>
            @endforeach
            @if (count($files) > 0)
            <div class="col-12">
                <h3>Files</h3>
            </div>
            @endif
            @foreach ($files as $file)
                <div class="col-2">
                    <div class="dropdown">
                        <button class="file btn d-flex align-items-center justify-content-center" data-bs-toggle="dropdown" aria-expanded="false">
                            <div>
                                <img src="{{ Storage::url('assets/file.png') }}" alt="File.png" class="img-fluid w-75">
                                <span class="text-black">{{ $file['directory']['filename'] }}.{{ $file['directory']['extension'] }}</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu">
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#view_details{{$file->id}}">View Details</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#rename_file{{$file->id}}">Rename File</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#update_file{{$file->id}}">Update File</button></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#revision_history{{$file->id}}">Revision History</button></li>
                            <li><a class="dropdown-item" id="download" href="{{route('download-evidence',[$file['id']])}}">Download</a></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#download_history{{$file->id}}">Download History</button></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#delete_file{{$file->id}}">Delete</button></li>
                        </ul>
                    </div>
                    {{-- View Details Modal --}}
                    <div class="modal fade" id="view_details{{$file->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                            <p class="text-center">{{ $file['directory']['filename'] }}.{{ $file['directory']['extension'] }}</p>
                                        </div>
                                        <div class="col-12">
                                            <span>Submitted by:</span>
                                            <p class="text-center">{{ $file['user']['firstname'] }} {{ $file['user']['surname'] }}</p>
                                        </div>
                                        <div class="col-12">
                                            <span>Uploaded on:</span>
                                            <p class="text-center">{{ date('F j, Y', strtotime($file['created_at'])) }}</p>
                                        </div>
                                        <div class="col-12">
                                            <span>Assigned:</span>
                                            <p class="text-center">{{ $file['process']['process_name'] }}</p>
                                        </div>
                                        @if (count($file['evidence_remarks']) > 0)
                                        <div class="col-12">
                                            <span>Recent Remarks</span>
                                            @foreach ($file['evidence_remarks'] as $remark)
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
                    {{-- Update Filename --}}
                    <div class="modal fade" id="rename_file{{$file->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">Update filename</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('rename-evidence') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="evidence" value="{{ $file['directory']['id'] }}">
                                        <div class="mb-2">
                                            <span>Filename</span>
                                            <input type="text" class="form-control" placeholder="Filename" name="filename" value="{{ $file['directory']['filename'] }}" required>
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
                    {{-- Update file --}}
                    <div class="modal fade" id="update_file{{$file->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">Update file Evidence</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('update-file-evidence') }}" id="area" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="file_id" value="{{ $file['directory']['id'] }}">
                                        <input type="hidden" name="evidence" value="{{ $file['id'] }}">
                                        <div class="mb-2">
                                            <span>Filename</span>
                                            <input type="text" class="form-control" placeholder="Filename" name="filename" required>
                                        </div>
                                        <div class="mb-2">
                                            <span>File</span>
                                            <input type="file" class="form-control" placeholder="File" name="file" required>
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
                    <div class="modal fade" id="revision_history{{$file->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">Revision History</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        @foreach ($file['evidence_histories'] as $history)
                                        <div class="col-12 text-center">
                                            <a target="_blank" href="{{ Storage::url($history['location']) }}" class="btn btn-link text-center">{{$history['filename']}}.{{$history['extension']}} - {{ date('F j, Y h:mA', strtotime($file['updated_at'])) }}</a>
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
                    <div class="modal fade" id="download_history{{$file->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">Download History</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        @foreach ($file['evidence_downloads'] as $download)
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
                    <div class="modal fade" id="delete_file{{$file->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5">File Details</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{route('delete-file-evidence')}}" method="post">
                                        <input type="hidden" name="id" value="{{ $file->id }}">
                                        @csrf
                                        <div class="text-center">
                                            <h3>Are you sure you want to delete {{ $file['directory']['filename'] }}.{{ $file['directory']['extension'] }}?</h3>
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
                </div>
            @endforeach
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="add" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add Evidence</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('upload-evidence') }}" id="area" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="parent" value="{{ Route::current()->parameter('parent') }}">
                        <input type="hidden" name="process" value="{{ Route::current()->parameter('process') }}">
                        <div class="mb-2">
                            <span>Filename</span>
                            <input type="text" class="form-control" placeholder="Filename" name="filename" required>
                        </div>
                        <div class="mb-2">
                            <span>File</span>
                            <input type="file" class="form-control" placeholder="File" name="file" required>
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
        const data = {!! json_encode($folders) !!};
        const data2 = {!! json_encode($files) !!};
        console.log(data);
        console.log(data2);
        document.addEventListener('DOMContentLoaded', function() {
            $('#download').on('click', function () {
                setTimeout(function() {
                    location.reload();
                }, 1000);
            });
        });
    </script>
@endsection