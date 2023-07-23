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
            <input type="date" id="date" class="form-control shadow-none" name="date" max="{{ date('Y-m-d') }}" />
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
            <label for="process" class="form-label">Process</label><span class="text-danger"> *
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
        <div style="text-align: right" class="pb-3">
          <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Submit</button>
        </div>
      </div>
    </form>
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

    var audit_plans = {!! json_encode($audit_plans) !!};

    $('#audit_plan').on('change', function(){
        var plan_id = parseInt($(this).val());
        
        $('#process').html('<option value="">Select Process</option>');
        if(plan_id != '') {
            var audit_plan = audit_plans.find(item => item.id === plan_id);
            audit_plan.plan_areas.forEach(function(i){
                $('#process').append(`<option value="` + i.area_id + `">` + i.area.parent.area_name + ` > ` + i.area.area_name + `</option`);
            }); 
        }
    });
</script>
@endsection