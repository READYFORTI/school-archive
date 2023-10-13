@extends('layout.sidebar')
@section('title')
    <title>Audit Evidence > {{ $audit_plan->name }}</title>
@endsection
@section('page')
    <div class="page-header">
        <h2>Audit Evidence > {{ $audit_plan->name }}</h2>
    </div>
    <div class="container">
        @include('layout.alert')
        <div class="mb-4 row">
            <div class="row col-12 mt-4">
                @foreach($areas as $area)
                    <div class="col-3">
                        <a href="{{ route('evidences') }}?directory={{ $area->directory->id }}" target="_blank">
                            <div class="card bg-success text-white">
                                <div class="card-body ">
                                    <div class="block-content block-content-full">
                                        <div class="justify-content-center py-3">
                                            <div class="fs-md fw-semibold text-uppercase">{{ $area->parent->area_name.' > '.$area->area_name ?? '' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
@endsection