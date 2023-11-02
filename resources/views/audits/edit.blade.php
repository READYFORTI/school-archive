@extends('layout.sidebar')
@section('title')
<title>View Audit Plan</title>
@endsection

  
@section('page')
    <div class="page-header">
        <h2>View Audit Plan</h2>
    </div>
    <div class="m-3 bg-white py-4 ">
        <div class="row mt-3 px-2 pb-3 m-3">
            @include('layout.alert')
            <div class="col-12">
                <button class="btn btn-danger btn-confirm px-2" style="float:right" data-message="Are you sure you wan't to delete this audit plan?" data-target="#delete_audit_plan"><i class="fa fa-trash"></i>  Delete Audit Plan</button>
                    <form id="delete_audit_plan" action="{{ route('lead-auditor.audit.delete', $audit_plan->id) }}" class="d-none" method="POST">
                        @csrf
                        @method('DELETE')
                    </form>
                </button>
            </div>
            <div class="col-12">
                <!-- <form method="POST" action="{{ route('lead-auditor.audit.update', $audit_plan->id) }}">
                    @csrf -->
                    <div>
                        <div class="mb-3">
                            <label for="process" class="form-label">Name</label><i class="text-danger"> *</i>
                            <input type="text" value="{{ $audit_plan->name ?? '' }}" class="form-control" id="name" name="name" placeholder="Enter name" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="process" class="form-label">Description</label><i class="text-danger"> *</i>
                            <textarea class="form-control" rows="3" id="description" name="description" placeholder="Enter description" readonly>{{ $audit_plan->description ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="process" class="form-label">Date</label><i class="text-danger"> *</i>
                            <input type="date" value="{{ $audit_plan->date ?? '' }}" class="form-control" id="date" name="date" placeholder="Enter date" readonly>
                        </div><br>
                    

                        <div class="accordion" id="accordionExample">
                            <div class="accordion-item">
                              <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="border: none; box-shadow: none;">
                                  <h6 class="text-success">PROCESS AND AUDITORS</h6>
                                </button>
                              </h2>
                              <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <table id="collapseOne" class="table text-black table-process accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <thead class="text-white bg-secondary text-uppercase"><tr><td>Process</td><td>Auditors</td></tr></thead>
                                            <tbody>
                                                @foreach($audit_plan->plan_areas as $plan_area)
                                                    <tr>
                                                        <td>{{ $plan_area->area->getAreaFullName() }}{{ $plan_area->area->area_name ?? '' }}</td>
                                                        <td>👩🏻‍💻
                                                            @foreach($plan_area->users as $user)
                                                                {{ $user->firstname ?? '' }} {{ $user->surname ?? ''}}
                                                                @if($loop->index < count($plan_area->users) - 1)
                                                                , 
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                    </table>
                                </div>
                              </div>
                            </div>

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="border: none; box-shadow: none;">
                                    <h6 class="text-success">CHECKLIST</h6>
                                  </button>
                                </h2>
                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                  <div class="accordion-body table-responsive">
                                      <table id="collapseTwo" class="table text-black table-process accordion-collapse collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <thead class="text-white text-uppercase bg-secondary">
                                            <tr>
                                                <td>Auditors</td>
                                                <td>Process</td>
                                                <td>Submitted Cars</td>
                                                <td>Submitted Audit Report</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($audit_plan->batches as $batch)
                                                <tr>
                                                    <td>👩🏻‍💻 {{ $batch->user_names() }}</td>
                                                    <td>{{ $batch->area_names() }}</td>
                                                    <td>
                                                        <!-- <span class="text-success">Done</span> -->
                                                        <span class="text-danger">Not Yet</span>
                                                    </td>
                                                    <td>
                                                        <!-- <span class="text-success">Done</span> -->
                                                        <span class="text-danger">Not Yet</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                      </table>
                                  </div>
                                </div>
                              </div>
                          </div>

                          

                          











                        {{-- <div class="mt-2">
                            <h5>Process and Auditors</h5>
                            <table class="table text-black table-process">
                                <thead class="text-white bg-secondary text-uppercase"><tr><td>Process</td><td>Auditors</td></tr></thead>
                                <tbody>
                                    @foreach($audit_plan->plan_areas as $plan_area)
                                        <tr>
                                            <td>{{ $plan_area->area->getAreaFullName() }}{{ $plan_area->area->area_name ?? '' }}</td>
                                            <td>
                                                @foreach($plan_area->users as $user)
                                                    {{ $user->firstname ?? '' }} {{ $user->surname ?? ''}}
                                                    @if($loop->index < count($plan_area->users) - 1)
                                                    , 
                                                    @endif
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table><br>
                        </div>
                    </div>
             
                <div class="col-12 mt-2">
                <h5 class="mb-2">Checklist</h5>
                <table class="table text-black table-process">
                    <thead class="text-white text-uppercase bg-secondary"><tr><td>Auditor</td><td>Process</td><td>File Type</td><td>Submitted</td><td>Time Submitted</td><td>Action</td></tr></thead>
                    <tbody>
                        @foreach($auditors as $user)
                            @foreach($user->audit_plan_area_user as $area_user)
                                <tr>
                                    <td>{{ sprintf("%s %s", $user->firstname ?? '', $user->surname ?? '') }}</td>
                                    <td>{{ sprintf("%s > %s", $area_user->audit_plan_area->area->parent->area_name ?? '', $area_user->audit_plan_area->area->area_name ?? 'None') }}</td>
                                    <td>
                                        {{ !empty($area_user->audit_report) && $area_user->audit_report->file->created_at != $area_user->audit_report->file->updated_at ? 'Revision AR' : 'Audit Report' }}
                                    </td>
                                    <td>{{ !empty($area_user->audit_report) ? 'YES' : 'Not Yet'}}</td>
                                    <td>{{ !empty($area_user->audit_report) ? $area_user->audit_report->updated_at->format('F d, Y h:i A') : '' }}</td>
                                    <td>
                                        @if(!empty($area_user->audit_report))
                                            <a href="{{ route('archives-show-file', $area_user->audit_report->file_id) }}" class="text-success" target="_blank"><i class="fa fa-eye"> View</i></a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ sprintf("%s %s", $user->firstname ?? '', $user->surname ?? '') }}</td>
                                    <td>{{ sprintf("%s > %s", $area_user->audit_plan_area->area->parent->area_name ?? '', $area_user->audit_plan_area->area->area_name ?? 'None') }}</td>
                                    <td>CARS</td>
                                    <td>{{ !empty($area_user->cars) ? 'YES' : 'Not Yet'}}</td>
                                    <td> {{ !empty($area_user->cars) ? $area_user->cars->created_at->format('F d, Y h:i A') : '' }}</td>
                                    <td>
                                        @if(!empty($area_user->cars))
                                            <a href="{{ route('archives-show-file', $area_user->cars->file_id) }}" class="text-success" target="_blank"><i class="fa fa-eye"> View</i></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                <table>
            </div> --}}


        </div>
              
        </div>
    </div>
@endsection

@section('js')
@endsection