@extends('layout.app')
@section('title')
<title>Document Archiving and Tracking</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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
        .progress-bar {
        border-radius: 5px;
        }

        .field-icon {
        float: right;
       
        margin-top: -30px;
        margin-right:8px;
        color: gray;
       
        }

        .container{
        padding-top:50px;
        margin: auto;
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
                        <img src="/storage/assets/dnsc-logo.png" alt="dnsc icon" class="img-fluid w-75">
                        <h3 class="text-center mt-2 text-success">Document Archiving and Tracking for DNSC QMS-ISO Undertakings</h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 p-3">
                <div class="d-flex align-items-center h-100">
                    <form enctype="multipart/form-data" action="{{ route('users.store') }}" method="POST" class="bg-white p-3 rounded w-100">
                        @csrf
                        @method('POST')
                        <div class=" mb-3 m-3 row">
                            <h3 class="text-center text-success">Register</h3><br><br><br>
                            <div class="row">
                            <div class="col-6 mt-3">
                                <span>First Name</span>
                                <span class="text-danger">*</span>
                                <input type="text" class="form-control shadow-none" name="firstname" placeholder="Enter first name" required value="{{ old('firstname') }}">
                                @error('firstname')
                                    <span class="text-danger error_firstname">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mt-3">
                                <span>Middle Name</span>
                                <input type="text" class="form-control shadow-none" name="middlename" placeholder="Enter middle name" value="{{ old('middlename') }}">
                                @error('middlename')
                                    <span class="text-danger error_middlename">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mt-3">
                                <span>Last Name</span>
                                <span class="text-danger">*</span>
                                <input type="text" class="form-control shadow-none" name="surname" placeholder="Enter last name" value="{{ old('surname') }}">
                                @error('surname')
                                    <span class="text-danger error_surname">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-6 mt-3">
                                <span>Suffix</span>
                                <input type="text" class="form-control shadow-none" name="suffix" placeholder="Enter suffix" value="{{ old('suffix') }}">
                                @error('suffix')
                                    <span class="text-danger error_suffix">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mt-3">
                                <span>Email</span>
                                <span class="text-danger">*</span>
                                <input type="email" class="form-control shadow-none" name="username" placeholder="Email" required value="{{ old('username') }}">
                                @error('username')
                                    <span class="text-danger error_username">{{ $message }}</span>
                                @enderror
                            </div>
                            
                                <div class="col-md-6 mb-3 mt-3">
                                    <label for="password">Password <span class="text-danger">*</span>
                                        <span id="password-strength"></span></label>
                                    <input type="password" class="form-control mb-1 shadow-none" name="password" id="password" placeholder="Password" onkeyup="isGood(this.value)" required>
                                    <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                                    <small class="help-block mt-3" id="password-text"></small>
                                </div>
                                <div class="col-md-6 mb-3 mt-3">
                                    <label for="password_confirmation">Confirm Password</label> <span class="text-danger">*</span>
                                    <input type="password" class="form-control shadow-none" name="password_confirmation" id="password_confirmation" placeholder="Confirm password" onkeyup="checkPasswordMatch()" required>
                                    @error('password')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                    <small class="text-success" id="password-match-text"></small>
                                </div>
                                
                                <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
                                <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
                                <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.4.2/zxcvbn.js"></script>
                                <script>
                                    function isGood(password) {
                                    var password_strength = document.getElementById("password-text");

                                    //TextBox left blank.
                                    if (password.length == 0) {
                                        password_strength.innerHTML = "";
                                        return;
                                    }

                                    //Regular Expressions.
                                    var regex = new Array();
                                    regex.push("[A-Z]"); //Uppercase Alphabet.
                                    regex.push("[a-z]"); //Lowercase Alphabet.
                                    regex.push("[0-9]"); //Digit.
                                    regex.push("[$@$!%-_*#?&]"); //Special Character.

                                    var passed = 0;

                                    //Validate for each Regular Expression.
                                    for (var i = 0; i < regex.length; i++) {
                                        if (new RegExp(regex[i]).test(password)) {
                                        passed++;
                                        }
                                    }

                                    //Display status.
                                    var strength = "";
                                    switch (passed) {
                                        case 0:
                                        case 1:
                                        case 2:
                                        strength = "<small class='progress-bar bg-danger' style='width: 40%'>Weak</small>";
                                        break;
                                        case 3:
                                        strength = "<small class='progress-bar bg-warning' style='width: 60%'>Medium</small>";
                                        break;
                                        case 4:
                                        strength = "<small class='progress-bar bg-success' style='width: 100%'>Strong</small>";
                                        break;

                                    }
                                    password_strength.innerHTML = strength;

                                    }

                                    $(".toggle-password").click(function() {

                                    $(this).toggleClass("fa-eye fa-eye-slash");
                                    var input = $($(this).attr("toggle"));
                                    if (input.attr("type") == "password") {
                                    input.attr("type", "text");
                                    } else {
                                    input.attr("type", "password");
                                    }
                                    });

                                    function checkPasswordMatch() {
                                    const password = document.getElementById('password').value;
                                    const confirmPassword = document.getElementById('password_confirmation').value;
                                    const matchText = document.getElementById('password-match-text');

                                    if (password === confirmPassword && password !== '') {
                                        matchText.textContent = 'Password Match';
                                        matchText.style.color = 'green';
                                    } else {
                                        matchText.textContent = '';
                                    }
                                }
                                </script>
                                
                                

                            
                            <div class="col-12 mt-3">
                                <span>Profile Picture</span>
                                <input type="file" class="form-control" name="img" placeholder="image" accept="image/png,image/jpg,image/jpeg">
                                @error('img')
                                    <span class="text-danger error_username">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-12 mt-3 mb-3 text-center">
                                <button type="submit" class="btn btn-success">Register</button>
                            </div>
                            <hr>
                            <div class="col-12 mt-3 text-center">
                                <a href="{{ route('login-page') }}" class="link">Already have an account</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
@vite(['resources/js/login.js'])
@endsection