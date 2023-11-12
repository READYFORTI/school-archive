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
    </div>

    <div class="container">
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('staff.template.index') }}">Template</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    Roles
                </li>
            </ol>
        </nav>
    </div>

    <div class="container mt-3">
        <div class="row">
            @foreach ($roles as $role)
            <div class="col-2">
                <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route('staff.template.roles.root',[$role->id]) }}">
                    {{ $role->role_name }}
                </a>
            </div> 
            @endforeach
        </div>
    </div>
@endsection
