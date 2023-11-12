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
    <div class="container">
        <h1>Evidence</h1>
        <div class="row g-3">
            @foreach ($assigned['process_user'] as $item)
                <div class="col-2">
                    @if ($item['office'])
                    <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route('po-office-process',$item['office']['id']) }}">
                        {{ $item['office']['office_name'] }}
                    </a>
                        
                    @else
                    <a class="folder btn d-flex align-items-center justify-content-center" href="{{ route('po-program-process',$item['program']['id']) }}">
                        {{ $item['program']['program_name'] }}   
                    </a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <script>
        const data = {!! json_encode($assigned) !!};
        console.log(data);
    </script>
@endsection