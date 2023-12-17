@extends('layout.sidebar')
@section('title')
<title>Audit</title>
@endsection
@section('page')
<div class="page-header">
    <h1>Audit Report</h1>
</div>
<div class="row m-3">
    @foreach($report_list as $report)
    <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <a href="{{ route('po.audit.download', $report->file_id) }}" class="text-decoration-none">
        <div class="card bg-secondary text-white">
            <div class="card-body">
            <div class="block-content block-content-full d-flex justify-content-center">
                <i class="fas fa-book-open fa-4x text-whitee"></i>
            </div>
            </div>
            <div class="card-footer d-flex justify-content-center">{{ $report->name ?? '' }}</div>
        </div>
        </a>
    </div>
    @endforeach
</div>
@endsection