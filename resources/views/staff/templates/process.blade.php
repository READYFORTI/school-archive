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

        }
    </style>
    {{-- Transaction Messages --}}
    <div class="container">
        <nav aria-label="breadcrumb" class="mt-3">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dcc-show-evidence') }}">Area</a></li>
                @if (isset($office))
                <li class="breadcrumb-item active" aria-current="page">{{ $office }}</li>
                @else
                <li class="breadcrumb-item active" aria-current="page">{{ $program }}</li>
                @endif
            </ol>
        </nav>
        {{-- List --}}
        <div class="row">
            @if (isset($office))
                @foreach ($processes as $item)
                <div class="col-2">
                    {{-- {{ $item }} --}}
                    <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route('dcc-show-office-process',$office).'/'.$item->id.'/' }}">
                        {{ $item->process_name }}
                    </a>
                </div>
                @endforeach
            @else
                @foreach ($processes as $item)
                <div class="col-2">
                    {{-- {{ $item }} --}}
                    <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route('dcc-show-program-process',$program).'/'.$item->id.'/' }}">
                        {{ $item->process_name }}
                    </a>
                </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
