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
                    </div>
                

                    <div class="accordion" id="accordionSection">
                        <div class="accordion-item">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingChecklist">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChecklist" aria-expanded="false" aria-controls="collapseChecklist" style="border: none; box-shadow: none;">
                                    <h6 class="text-success">CHECKLIST</h6>
                                    </button>
                                </h2>
                                <div id="collapseChecklist" class="accordion-collapse collapse show" aria-labelledby="headingChecklist" data-bs-parent="#accordionSection">
                                    <div class="accordion-body show table-responsive">
                                        <table class="table text-black table-process">
                                        <thead class="text-white text-uppercase bg-secondary">
                                            <tr>
                                                <td>Auditors</td>
                                                <td>Process</td>
                                                <td>Submitted Cars</td>
                                                <td>Submitted Audit Report</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($batches as $batch)
                                                <tr>
                                                    <td>👩🏻‍💻 {{ $batch->user_names() }}</td>
                                                    <td>{{ $batch->area_names() }}</td>
                                                    <td>
                                                        @if($batch->cars)
                                                            <span class="text-success">YES</span>
                                                        @else
                                                            <span class="text-danger">NO</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($batch->audit_report)
                                                            <span class="text-success">Done</span>
                                                        @else
                                                            <span class="text-danger">Not Yet</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        </table>
                                    </div>
                                </div>
                                </div>
                            </div>
                            <h2 class="accordion-header" id="headingProcess">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseProcess" aria-expanded="true" aria-controls="collapseProcess" style="border: none; box-shadow: none;">
                                <h6 class="text-success">PROCESS AND AUDITORS</h6>
                            </button>
                            </h2>
                            <div id="collapseProcess" class="accordion-collapse collapse" aria-labelledby="headingProcess" data-bs-parent="#accordionSection">
                            <div class="accordion-body">
                                <table class="table text-black table-process">
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
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
@endsection