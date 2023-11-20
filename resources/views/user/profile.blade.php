@extends('layout.sidebar')
@section('title')
    <title>Profile</title>
@endsection

@section('page')
    <div class="page-header pb-2">
        <h2>User Profile</h2>
    </div>
    <style>
        .gradient-custom {
        /* fallback for old browsers */
        background: #98e288;

        /* Chrome 10-25, Safari 5.1-6 */
        background: -webkit-linear-gradient(to right bottom, rgb(77, 215, 82), rgb(50, 107, 46));

        /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
        background: linear-gradient(to right bottom, rgb(135, 246, 101), rgb(41, 117, 85))
        }
        
    .custom-btn-color {
        background-color: #2f7257; /* Set the background color to #18493a */
       
    }
    </style>
    <div class="m-3 py-5">
    <div class="row d-flex justify-content-center align-items-center h-100" >
        <div class="col col-lg-6 mb-4 mb-lg-0 mt-0"style="width: 90%; heigth: 100%">
        <div class="card mb-3" style="border-radius: 10px;">
            <div class="row g-0">
            <div class="col-md-4 gradient-custom text-center text-white" style="border-top-left-radius: 10px; border-bottom-left-radius: 10px ">
                <img src="{{ Storage::url($user->img) }}" alt="Avatar" class="img-fluid my-4 rounded-circle" style="width: 180px;">
                <h5 class="text-warning">{{ $user->firstname }} {{ $user->surname }}</h5>
                <p>{{ $user->role->role_name }}</p>
                @if(in_array($user->role->role_name, config('app.role_with_assigned_area')))
                        <div class="col-12 mt-3">
                            <span>Assigned on</span>
                            @if(!empty($user->assigned_areas))
                                @foreach($user->assigned_areas as $area)
                                    <br/>{{ sprintf("%s > %s", $area->parent->area_name ?? '', $area->area_name ?? 'None') }}
                                @endforeach
                            @else
                                {{ $user->assigned_area->area_name ?? 'None' }}
                            @endif
                        </div>
                    @endif
                    <small>
                        <a href="{{ route('users.update', $user->id) }}" class="text-white btn btn-info custom-btn-color btn-sm">
                            <i class="fas fa-cog"> Update Profile</i>
                        </a>
                        <br><br>
                    </small>
            </div>
            <div class="col-md-8">
                <div class="card-body p-4">
                    <h6 class="text-warning">INFORMATION</h6>
                    <hr class="mt-0 mb-4">
                    <div class="row pt-1">
                        <div class="col-6 mb-3">
                        <h6>Email</h6>
                        <p class="text-muted">{{ $user->username }}</p>
                        </div>
                        <div class="col-6 mb-3">
                        <h6>Phone</h6>
                        <p class="text-muted">123 456 789</p>
                        </div>
                    </div>
                    <h6 class="text-warning">OTHER</h6>
                    <hr class="mt-0 mb-4">
                    <div class="row pt-1">
                        <div class="col-6 mb-3">
                        <h6>Position</h6>
                        <p class="text-muted">{{ $user->role->role_name }}</p>
                        </div>
                        <div class="col-6 mb-3">
                        <h6>Joined</h6>
                        <p class="text-muted">{{ $user->created_at }}</p>
                        </div>
                    </div>
                    <div class="d-flex justify-content-start">
                        <a href="#!"><i class="fab fa-facebook-f fa-lg me-3"></i></a>
                        <a href="#!"><i class="fab fa-twitter fa-lg me-3"></i></a>
                        <a href="#!"><i class="fab fa-instagram fa-lg"></i></a>
                    </div>
                </div>
            </div>
            </div>
        </div>
        </div>
    </div>
    </div>
@endsection



        