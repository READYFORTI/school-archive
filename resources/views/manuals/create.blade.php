@extends('layout.sidebar')

@section('title')
<title>Add Manual</title>
@endsection

@section('page')
<div class="page-header">
    <h2>Add Manual</h2>
</div>
<div class="m-3 bg-white py-4">
    <div class="m-3">
        @include('layout.alert')
        <form method="POST" action="{{ route('po.manual.store') }}" enctype="multipart/form-data" class="row">
            @csrf
            <div class="col-lg-6 col-md-12 mb-3">
                <label for="name" class="form-label">Manual Name</label><span class="text-danger"> *</span>
                <input type="text" class="form-control shadow-none" name="name" id="name" placeholder="Enter Manual Name" required>
            </div>
            <div class="col-lg-6 col-md-12 mb-3">
                <label for="date" class="form-label">Date</label><span class="text-danger"> *</span>
                <input type="date" id="date" class="form-control shadow-none" name="date" max="{{ date('Y-m-d') }}">
            </div>
            <div class="col-12 mb-3">
                <label for="search" class="form-label">Description</label><span class="text-danger"> *</span>
                <textarea name="description" class="form-control shadow-none" rows="3"></textarea>
            </div>
            @if(Auth::user()->role->role_name == 'Process Owner')
            <div class="col-lg-6 col-md-12 mb-3">
                <label for="directory" class="form-label">
                    Folder (If empty, coordinate with DCC)
                    <i class="bi bi-question-circle-fill text-info" data-bs-toggle="tooltip" data-bs-placement="top" title="This short text will be placed after Product Quantity."></i>
                    <span class="text-danger">*</span>
                </label>
                <select id="directory" name="directory" class="form-control shadow-none" required>
                    <option value="">Select Folder</option>
                    @foreach($directories as $directory)
                    <option value="{{ $directory->id }}">
                        @if(in_array(auth()->user()->role->role_name, ['Process Owner', 'Internal Auditor']))
                        {{ sprintf('%s%s%s', $directory->parent->parent->name ? $directory->parent->parent->name.' > ' : '', $directory->parent->name ? $directory->parent->name.' > ' : '', $directory->name ?? '') }}
                        @else
                        {{ $directory->name ?? '' }}
                        @endif
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="col-lg-6 col-md-12 mb-3">
                <label for="file_attachments" class="form-label">Attachment</label><span class="text-danger"> *</span>
                <input type="file" class="form-control shadow-none" name="file_attachments[]" id="file_attachments" required multiple accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
            </div>
            <div class="col-12 d-flex justify-content-end mb-3">
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Add Manual</button>
            </div>
        </form>
    </div>
</div>
@endsection

