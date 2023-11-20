@extends('layout.sidebar')
@section('title')
<title>Add Annoucement</title>
@endsection
@section('page')
    <div class="page-header">
        <h2>Add Annoucement</h2>
    </div>
    <div class="m-3 bg-white py-4">
        <div class="row mt-3 px-2 m-3">
            @include('layout.alert')
            <form method="POST" action="{{ route('admin-announcement-store') }}">
                @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Annoucement Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date:</label>
                        <input type="date" id="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"/>
                    </div>
                    <div class="mb-3">
                        <label for="search" class="form-label">Description:</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div style="text-align: right" class="pb-3">
                    <button type="submit" class="btn btn-success">Submit</button>
                </div>
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
    });
</script>
@endsection