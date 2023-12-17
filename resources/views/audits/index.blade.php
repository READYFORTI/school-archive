</div><!DOCTYPE html>
<html lang="en">
<head>
  <!-- Include necessary Bootstrap CSS and other dependencies -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  
</head>

<body>
@extends('layout.sidebar')
@section('title')

@php
    $title = auth()->user()->role->role_name == 'Internal Lead Auditor' ? 'Audit Plans' : 'Audit Evidence';
@endphp
<title>{{ $title }}</title>
@endsection
@section('page')
    <div class="page-header">
        <h2>{{ $title }}</h2>
    </div>
    @if(auth()->user()->role->role_name == 'Internal Lead Auditor')
    <div class="text-end  mr-3 p-3">
      <small><a href="{{ route('lead-auditor.audit.create') }}" class="btn btn-success"><i class="fa fa-plus"></i> New Plan</a></small>
      @if(!empty($audit_plans) && count($audit_plans) > 0)
      <small><a href="{{ route('lead-auditor.audit.previous') }}" class="btn btn-warning "><i class="fa fa-edit"></i> Previous Plan</a></small>
      @endif
    </div>
    @endif
    
        
        
  
  <div class="container-fluid ">
    @if (session('message'))
      {!! session('message') !!}
    @endif
    <div class="row">
      <div class="col-lg-9">
        <div class="m-3 bg-transparent py-2">
          @if(auth()->user()->role->role_name == 'Internal Lead Auditor')
          <div class="row">
            <div class="col-12">
              <div class="row mb-4 m-1">
                @foreach($audit_plans as $plan)
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                  <a href="{{ route('lead-auditor.audit.edit', $plan->id) }}" class="text-decoration-none">
                    <div class="card bg-secondary text-white">
                      <div class="card-body">
                        <div class="block-content block-content-full d-flex justify-content-center">
                          <i class="fas fa-book-open fa-4x text-whitee"></i>
                        </div>
                      </div>
                      <div class="card-footer d-flex justify-content-center">{{ $plan->name ?? '' }}</div>
                    </div>
                  </a>
                  @if (!$plan->audit_plan_file)
                    <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-secondary mt-1 w-100">Upload File</button>
                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                      <div class="modal-dialog modal-dialog-centered">
                        <form class="modal-content" enctype="multipart/form-data" method="POST" action="{{ route('lead-auditor.audit.file') }}">
                          <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Upload File</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            @csrf
                            <div class="mb-2">
                              <label class="form-label" for="audit_plan">File Name</label>
                              <input type="text" name="filename" class="form-control" required>
                            </div>
                            <div class="mb-2">
                              <label class="form-label" for="audit_plan">Audit Plan File</label>
                              <input type="file" name="audit_plan_file" class="form-control" required>
                              <input type="hidden" name="audit_plan_id" value="{{ $plan->id }}">
                            </div>
                          </div>
                          <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-success">Save</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  @else
                    <a href="{{route('audit.file.download',$plan->id)}}" target="_blank" class="btn btn-secondary w-100 mt-1">{{$plan->audit_plan_file->file_name}}</a>
                  @endif
                </div>
                @endforeach
              </div>
            </div>
          </div>
          @else
          <div class="row">
            <div class="col-12">
              <div class="row mb-4">
                @foreach($audit_plans as $plan)
                <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
                  <a href="{{ route('auditor.audit.evidence.show', $plan->id) }}" class="text-decoration-none">
                    <div class="card bg-success text-white">
                      <div class="card-body">
                        <div class="block-content block-content-full d-flex justify-content-center">
                          <i class="fas fa-book-open fa-4x text-warning"></i>
                        </div>
                      </div>
                      <div class="card-footer d-flex justify-content-center">{{ $plan->name ?? '' }}</div>
                    </div>
                  </a>
                </div>
                @endforeach
              </div>
            </div>
          </div>
          @endif
        </div>
      </div>

      
      @if(auth()->user()->role->role_name == 'Internal Lead Auditor')
      <div class="col-lg-3">
        <div class="m-3 bg-white py-2">
            <button class="btn btn text-success" type="button" data-toggle="collapse" data-target="#internal-auditors" aria-expanded="true" aria-controls="internal-auditors" style="border: none; box-shadow: none;">
              <i class="fas fa-bars"></i>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;INTERNAL AUDITORS
            </button>

            <div class="collapse show m-3" id="internal-auditors" style="flex-direction: row-reverse;">
              @if(auth()->user()->role->role_name == 'Internal Lead Auditor')
              <div class="card bg-light border-0">
                <div class="card-body p-3">
                  @foreach($auditors as $user)
                  <div class="media align-items-center mb-4">
                    <img src="{{ Storage::url($user->img) }}" alt="Avatar" class="rounded-circle mr-3" alt="Profile Image" width="50">
                    
                    <div class="media-body">
                      <h6 class="mt-0 text-primary">{{ sprintf("%s %s", $user->firstname ?? '', $user->surname ?? '') }}</h6>
                      <p class="mb-0 text-success small">Assigned on:</p>
                      <ul class="list-unstyled mb-0 text-muted small">
                        @foreach($user->getAssignedAreas() as $assignedArea)
                        <li class="mb-1">{{ $assignedArea }}</li>
                        @endforeach
                      </ul>
                    </div>
                  </div>
                  @endforeach
                </div>
              </div>
              @endif
            </div>
            
        </div>
      </div>
      @endif
    </div>
</div>

        
    
      <!-- Include necessary Bootstrap JS and jQuery dependencies -->
      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    </body>
    </html>
    
@endsection