@extends('layout.app')
@section('title')
<title>Document Archiving and Tracking</title>
@endsection
@section('css')
    <style>
        .h-fit{
            height: fit-content
        }
        .link{
            color: black;
            text-decoration: none;
        }
        .link:hover{
            color: #198754;
        }
        
        body {
            background: url('{{ asset("/media/bgbg.jpg") }}') no-repeat center center fixed; 
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
        
    </style>
@endsection
@section('content')
    <div style="height:100vh">
        <div class="container h-100">
            <div class="row h-100">
                
                <div class="col-lg-6">
                    <div class="container h-100 d-flex align-items-center">
                        <div class="h-fit text-center">
                            <img src="/storage/assets/dnsc-logo.png" alt="dnsc icon" class="img-fluid w-50">
                            <h3 class="text-center mt-3 text-success text-lg">DOCUMENT ARCHIVING AND TRACKING FOR ISO</h3>
                            {{-- <h3 class="text-center mt-3 text-success display-4">DOCUMENT ARCHIVING AND TRACKING FOR ISO</h3> --}}

                        </div>
                    </div>
                </div>
                <div class="col-lg-6 p-3">
                    <div class="d-flex align-items-center h-100">
                        <form action="{{ route('login') }}" method="POST" class="bg-white p-3 rounded w-100">
                            @csrf
                            @method('POST')
                            <div class="container mt-3 mb-3">
                                <h3 class="text-center text-success">Welcome!</h3>
                                <div class="mt-3">
                                    <span>Username</span>
                                    <input type="text" class="form-control" name="username" placeholder="username" required value="{{ old('username') }}">
                                    @error('username')
                                        <span class="text-danger error_username">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mt-3">
                                    <span>Password</span>
                                    <input type="password" class="form-control" name="password" placeholder="password" required value="{{ old('password') }}">
                                </div>
                                <div class="mt-3 text-center">
                                    <button type="submit" class="btn btn-success">Login</button>
                                </div>
                                <hr>
                                <div class="mt-3 text-center">
                                    <a href="{{ route('surveys.create') }}" class="btn btn-primary">Answer Survey Here</a>
                                </div>
                                <hr>
                                <div class="mt-3 text-center">
                                    <a href="{{ route('users.create') }}" class="link">Register</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Transaction Messages --}}
    @if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title:'{{ session('success') }}',
                icon:'success'
            });
        });   
    </script>
    @endif
@endsection
@section('js')
@vite(['resources/js/login.js'])
@endsection