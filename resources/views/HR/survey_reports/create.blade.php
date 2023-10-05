@extends('layout.sidebar')
@section('title')
<title>Add Survey Report</title>
@endsection
@section('page')
    <div class="page-header">
        <h2>Add Survey Report</h2>
    </div>
    <div class="m-3 bg-white py-4">
        {{-- <div class="m-3"> --}}
        <div class="m-3">
            @include('layout.alert')
            <form method="POST" action="{{ route('hr.survey_report.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Report Name</label><span class="text-danger"> *</span>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Report Name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="name" class="form-label">Office</label><span class="text-danger"> *</span>
                        <select name="facility" class="form-control" required>
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                <div><br>

                    <div class="mb-3">
                        <label for="search" class="form-label">Description</label><span class="text-danger"> *</span>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="date" class="form-label">Date</label><span class="text-danger"> *</span>
                        <input type="date" id="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"/>
                    </div>
                    <div class="col-md-6">
                        <label for="file_attachments" class="form-label">Attachment</label><span class="text-danger"> *</span>
                        <input type="file" class="form-control" name="file_attachments[]" id="file_attachments" 
                            required multiple accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </div>
                </div>
                </div>
                <div style="text-align: right">
                    <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Submit</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('js')
<script>
    $("#date").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        maxDate: "{{ date('Y-m-d') }}"
    });
</script>
@endsection