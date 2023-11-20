@extends('layout.sidebar')
@section('title')
<title>Update Profile</title>
@endsection
@section('page')

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<div class="page-header">
  <h2>Update User Information</h2>
</div>
  <div class="m-3">
    @include('layout.alert')
    @if (session('success'))
    @endif
            <form enctype="multipart/form-data" action="{{ route('users.update', $user->id) }}" method="POST" class="bg-white p-3 rounded center-form">
                @csrf
                @method('PUT')
                <div class=" mt-3 mb-3 row">
                    <div class="row">
                        <div class="form-group col-6 mt-3">
                            <span>Firstname</span>
                            <input type="text" class="form-control" name="firstname" value="{{ $user->firstname }}" required>
                            @error('firstname')
                                <span class="text-danger error_firstname">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6 mt-3">
                            <span>Middlename</span>
                            <input type="text" class="form-control" name="middlename" value="{{ $user->middlename }}">
                            @error('middlename')
                                <span class="text-danger error_middlename">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6 mt-3">
                            <span>Surename</span>
                            <input type="text" class="form-control" name="surname" value="{{ $user->surname }}">
                            @error('surname')
                                <span class="text-danger error_surname">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-6 mt-3">
                            <span>Suffix</span>
                            <input type="text" class="form-control" name="suffix" value="{{ $user->suffix }}">
                            @error('suffix')
                                    <span class="text-danger error_suffix">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group col-12 mt-3">
                            <span>Username</span>
                            <input type="text" class="form-control" name="username" value="{{ $user->username }}" required>
                            @error('username')
                                    <span class="text-danger error_username">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="col-6 mt-3">
                            <div class="tooltip-container">
                                <button class="btn btn-outline-warning btn-sm" type="button" data-toggle="collapse" data-target="#passwordAccordion">
                                    Change Password<span class="tooltip-trigger" data-toggle="tooltip" data-placement="top" title="Leave it blank if you don't want to change your password!"> <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-question-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                        <path d="M5.255 5.786a.237.237 0 0 0 .241.247h.825c.138 0 .248-.113.266-.25.09-.656.54-1.134 1.342-1.134.686 0 1.314.343 1.314 1.168 0 .635-.374.927-.965 1.371-.673.489-1.206 1.06-1.168 1.987l.003.217a.25.25 0 0 0 .25.246h.811a.25.25 0 0 0 .25-.25v-.105c0-.718.273-.927 1.01-1.486.609-.463 1.244-.977 1.244-2.056 0-1.511-1.276-2.241-2.673-2.241-1.267 0-2.655.59-2.75 2.286zm1.557 5.763c0 .533.425.927 1.01.927.609 0 1.028-.394 1.028-.927 0-.552-.42-.94-1.029-.94-.584 0-1.009.388-1.009.94z"/>
                                      </svg></span>
                                </button>
                                @error('password')
                                    <span class="text-danger error_password">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>



                        <div id="passwordAccordion" class="collapse">
                            <div class="col-6 mt-3">
                                <span>Password</span>
                                <input type="password" class="form-control" name="password" placeholder="Password"  value="{{ old('password') }}">
                                {{-- @error('password')
                                    <span class="text-danger error_password">{{ $message }}</span>
                                @enderror --}}
                            </div>
                            <div class="col-6 mt-3">
                                <span>Confirm Password</span>
                                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password"  value="{{ old('password_confirmation') }}">
                                @error('password_confirmation')
                                    <span class="text-danger error_password_confirmation">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <span>Image</span>
                            <input type="file" class="form-control" name="img" accept="image/png, image/jpg, image/jpeg">
                        </div>
                        <div class="col-12 mt-3 text-center">
                            <button type="submit" class="btn btn-success">Update</button>
                        </div>
                </div>
            </form>
        </div>
        @endsection
        @section('js')
        @vite(['resources/js/login.js'])
        @endsection