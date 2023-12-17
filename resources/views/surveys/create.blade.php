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
        .steps {
            display: none !important;
        }
        body {   
            background: url('{{ asset("/media/bgbg.jpg") }}') no-repeat center center fixed; 
            -webkit-background-size: cover;
            -moz-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
    </style>
    <link rel="stylesheet" href="packages/jquery.steps-1.1.0/jquery.steps.css">
@endsection
@section('content')
    <div style="padding-top: 30px;">
        <div class="green-line align-center text-center">
            <img src="/storage/assets/dnsc-logo.png" width="200px" alt="dnsc icon" class="img-fluid">
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12 p-3">
                <div class="d-flex align-items-center">
                    <form id="survey-form" enctype="multipart/form-data" action="{{ route('surveys.store') }}" method="POST" class="bg-white p-3 rounded w-100">
                        @csrf
                        @method('POST')
                        <div class="container mt-3 mb-3">
                            <h3 class="text-center text-success">Survey Form</h3>
                            <div class="survey-container mt-4">
                                <h3>Personal Details</h3>
                                <section>
                                    <h4>Personal Details</h4>
                                    <div class="mt-3">
                                        <label>Fullname</label>
                                        <input type="text" class="form-control" name="fullname" placeholder="Enter Fullname" required value="{{ old('fullname', $prevSurvey->name ?? '') }}" required>
                                        @error('fullname')
                                            <span class="text-danger error_fullname">{{ $message }}</span>
                                        @enderror
                                    </div> 
                                    <div class="mt-3">
                                        <label>Contact Number</label>
                                        <input type="text" class="form-control" name="contact_number" placeholder="Enter Contact Number" value="{{ old('contact_number', $prevSurvey->contact_number ?? '') }}">
                                        @error('contact_number')
                                            <span class="text-danger error_contact_number">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mt-3">
                                        <label>Email</label>
                                        <input type="email" class="form-control" name="email" placeholder="Enter Email" value="{{ old('email', $prevSurvey->email ?? '') }}" required>
                                        @error('email')
                                            <span class="text-danger error_email">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="type Student">
                                        <div class="mt-3">
                                            <label>Course</label>
                                            <input type="text" class="form-control" name="course" placeholder="Enter course" value="{{ old('course') }}" required>
                                            @error('course')
                                                <span class="text-danger error_course">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mt-3">
                                            <label>Year</label>
                                            <select name="course_year" class="form-control" required>
                                                <option value="">Select Year</option>
                                                @for($y = date('Y'); $y >= 1950; $y--)
                                                    <option value="{{ $y }}">{{ $y }}</option>
                                                @endfor
                                            </select>
                                            @error('course_year')
                                                <span class="text-danger error_course_year">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="type Visitor">
                                        <div class="mt-3">
                                            <label>Occupation</label>
                                            <input type="text" class="form-control" name="occupation" placeholder="Enter occupation" value="{{ old('occupation') }}" required>
                                            @error('occupation')
                                                <span class="text-danger error_occupation">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </section>
                                
                                <h3>Offices</h3>
                                <section>
                                    <h4>Office</h4>
                                    <div class="mt-3">
                                        <select name="office" class="form-control" required>
                                            <option value="">Select Office</option>
                                            @foreach($facilities as $facility)
                                                <option value="{{ $facility->id }}">{{ $facility->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('office')
                                            <span class="text-danger error_type">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mt-3">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <td>Service Criteria</td>
                                                    <td>1</td>
                                                    <td>2</td>
                                                    <td>3</td>
                                                    <td>4</td>
                                                    <td>5</td>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Promptness of Service:</td>
                                                    <td><input type="radio" name="promptness" value="1" required/></td>
                                                    <td><input type="radio" name="promptness" value="2" required/></td>
                                                    <td><input type="radio" name="promptness" value="3" required/></td>
                                                    <td><input type="radio" name="promptness" value="4" required/></td>
                                                    <td><input type="radio" name="promptness" value="5" required/></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">
                                                        <label id="promptness-error" class="d-hide error" for="promptness"><br/>This field is required.</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Quality of Engagement:</td>
                                                    <td><input type="radio" name="engagement" value="1" required/></td>
                                                    <td><input type="radio" name="engagement" value="2" required/></td>
                                                    <td><input type="radio" name="engagement" value="3" required/></td>
                                                    <td><input type="radio" name="engagement" value="4" required/></td>
                                                    <td><input type="radio" name="engagement" value="5" required/></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">
                                                        <label id="engagement-error" class="d-hide error" for="engagement"><br/>This field is required.</label>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Cordiality of Personnel:</td>
                                                    <td><input type="radio" name="cordiality" value="1" required/></td>
                                                    <td><input type="radio" name="cordiality" value="2" required/></td>
                                                    <td><input type="radio" name="cordiality" value="3" required/></td>
                                                    <td><input type="radio" name="cordiality" value="4" required/></td>
                                                    <td><input type="radio" name="cordiality" value="5" required/></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6">
                                                        <label id="cordiality-error" class="d-hide error" for="cordiality"><br/>This field is required.</label>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </section>

                                <h3>Comments/Suggestions</h3>
                                <section>
                                    <h4>Comments/Suggestions</h4>
                                    <textarea class="form-control" name="suggestions" rows="5">{{ old('suggestions') }}</textarea>
                                </section>
                            </div>
                            <hr>
                            <div class="mt-3 text-center">
                                <a href="{{ route('login-page') }}" class="link">Back to Login</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@vite(['resources/js/login.js'])
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-steps/1.1.0/jquery.steps.js"></script>

<script>
    var form = $("#survey-form");
    $(".survey-container").steps({
        headerTag: "h3",
        bodyTag: "section",
        transitionEffect: "slideLeft",
        autoFocus: true,
        onStepChanging: function (event, currentIndex, newIndex)
        {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinishing: function (event, currentIndex)
        {
            form.validate().settings.ignore = ":disabled,:hidden";
            return form.valid();
        },
        onFinished: function (event, currentIndex)
        {
            form.submit();
        }
    });

    $('div.type').hide();
    $('.select-type').on('change',function(){
        $('div.type').hide();
        $('div.type.' + this.value).show();
    });
</script>
@endsection