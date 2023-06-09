@extends('layout.sidebar')
@section('title')
<title>Add Survey Report</title>
@endsection
@section('page')
    <div class="page-header">
        <h2>Add Survey Report</h2>
    </div>
    <div class="container">
        {{-- <div class="m-3"> --}}
        <div class="row mt-3 px-3">
            @include('layout.alert')
            <form method="POST" action="{{ route('hr.survey_report.store') }}" enctype="multipart/form-data">
                @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Report Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Report Name" required>
                    </div>
                    <div class="mb-3">
                        <select name="facility" class="form-control" required>
                            <option value="">Select Office</option>
                            @foreach($offices as $office)
                                <option value="{{ $office->id }}">{{ $office->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="search" class="form-label">Description:</label>
                        <textarea name="description" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date:</label>
                        <input type="date" id="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"/>
                    </div>
                    <div class="mb-3">
                        <label for="file_attachments" class="form-label">Attachment</label>
                        <input type="file" class="form-control" name="file_attachments[]" id="file_attachments" 
                            required multiple accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </div>
                </div>
                <div style="text-align: right">
                    <button type="submit" class="btn btn-success">Submit</button>
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