@extends('layout.sidebar')
@section('title')
<title>Add Audit Report</title>
@endsection
@section('page')
<div class="page-header">
  <h2>Add Audit Report</h2>
</div>

  <div class="m-3">
    @include('layout.alert')
    <form method="POST" action="{{ route('auditor.audit-reports.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="card p-4">
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="name" class="form-label">Name</label><span class="text-danger"> *
            <input type="text" class="form-control shadow-none" name="name" id="name" placeholder="Enter Audit Report Name" required>
          </div>
          <div class="col-md-6">
            <label for="date" class="form-label">Date</label><span class="text-danger"> *
            <input type="date" id="date" class="form-control shadow-none date" name="date" max="{{ date('Y-m-d') }}" />
          </div>
        </div>
        <div class="mb-3">
          <label for="search" class="form-label">Description</label><span class="text-danger"> *
          <textarea name="description" class="form-control shadow-none" rows="3"></textarea>
        </div>
        <div class="row mb-3">
          <div class="col-md-6">
            <label for="audit_plan" class="form-label">Audit Plan</label><span class="text-danger"> *
            <select id="audit_plan" name="audit_plan" class="form-control shadow-none" required>
              <option value="">Select Audit Plan</option>
              @foreach($audit_plans as $audit_plan)
                <option value="{{ $audit_plan->id }}">{{ $audit_plan->name ?? '' }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label for="process" class="form-label">Audit Process</label><span class="text-danger"> *
            <select id="process" name="process" class="form-control shadow-none" required>
              <option value="">Select Process</option>
            </select>
          </div>
        </div>
        <div class="mb-3">
          <label for="file_attachments" class="form-label">Attachment</label><span class="text-danger"> *
          <input type="file" class="form-control shadow-none" name="file_attachments[]" id="file_attachments" required multiple
            accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
        </div>

        <div class="form-check">
          <input class="form-check-input" name="with_cars" type="checkbox" value="" id="with_cars">
          <label class="form-check-label" for="with_cars">
            With CARS
          </label>
        </div>


        <div class="cars-section d-none">
            <hr width="100%"/>
            <h4>CARS Details</h4>
            <div class="px-2">
              <div class="mb-3">
                  <label for="name" class="form-label">Name</label>
                  <input type="text" class="form-control" name="cars_name" id="cars_name" placeholder="Enter Filename">
              </div>
              <div class="mb-3">
                  <label for="date" class="form-label">Date:</label>
                  <input type="date" id="date" class="form-control date" name="cars_date" max="{{ date('Y-m-d') }}"/>
              </div>
              <div class="mb-3">
                  <label for="search" class="form-label">Description:</label>
                  <textarea name="cars_description" id="cars_description" class="form-control" rows="3"></textarea>
              </div>
              <div class="mb-3">
                  <label for="file_attachments" class="form-label">Attachment</label>
                  <input type="file" id="cars_file" class="form-control" name="cars_file_attachments[]" 
                      id="file_attachments2" multiple
                      accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
              </div>
            </div>
        </div>

        <div style="text-align: right" class="pb-3">
          <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Submit</button>
        </div>
      </div>
    </form>
  </div>

@endsection




@section('js')
<script>
    $(".date").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        maxDate: "{{ date('Y-m-d') }}"
    });

    var audit_plans = {!! json_encode($audit_plans) !!};

    $('#audit_plan').on('change', function(){
        var plan_id = parseInt($(this).val());
        
        $('#process').html('<option value="">Select Process</option>');
        if(plan_id != '') {
            var audit_plan = audit_plans.find(item => item.id === plan_id);
            audit_plan.batches.forEach(function(i){
                $('#process').append(`<option value="` + i.id + `">` + i.area_names + `</option`);
            }); 
        }
    });

    $('#with_cars').on('change', function(){
      $('.cars-section').addClass('d-none');
      if(this.checked) {
        $('.cars-section').removeClass('d-none');
        $('#cars_name').attr('required',true);
        $('#date').attr('required',true);
        $('#cars_description').attr('required',true);
        $('#file_attachments2').attr('required',true);
      }
      else{
        $('#cars_name').attr('required',false);
        $('#date').attr('required',false);
        $('#cars_description').attr('required',false);
        $('#file_attachments2').attr('required',false);
      }
    })
</script>
@endsection