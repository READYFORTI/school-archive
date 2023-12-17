@extends('layout.sidebar')
@section('title')
<title>Audit Evaluation</title>
@endsection
@section('page')
<div class="page-header">
    <h1>Audit Evaluation</h1>
</div>
<div class="row m-3">
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">Name of Auditor</label>
            <input type="text" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">IAQ No.</label>
            <input type="text" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">Process/Area Audited</label>
            <input type="text" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">Date Audited</label>
            <input type="date" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-8">
        <h6>***Note: Rate the auditor with 4 being the highest and 1 being the lowest.</h6>
    </div>
    <div class="col-4 text-end">
        <button type="button" class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fas fa-info"></i></button>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Definition</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-3">
                                <h6 class="text-start">Ethical Conduct</h6>
                            </div>
                            <div class="col-9">
                                <h6 class="text-start">- able to be diplomatic, open-minded and assertive</h6>
                            </div>
                            <div class="col-3">
                                <h6 class="text-start">Fair Presentation</h6>
                            </div>
                            <div class="col-9">
                                <h6 class="text-start">- absolutely unbiased</h6>
                            </div>
                            <div class="col-3">
                                <h6 class="text-start">Due Professional Care</h6>
                            </div>
                            <div class="col-9">
                                <h6 class="text-start">- diligence which a person, who possesses a special skill, under a given set of circumstances</h6>
                            </div>
                            <div class="col-3">
                                <h6 class="text-start">Independence</h6>
                            </div>
                            <div class="col-9">
                                <h6 class="text-start">- able to deliver questions and report directly honestly</h6>
                            </div>
                            <div class="col-3">
                                <h6 class="text-start">Evidence-Based approach</h6>
                            </div>
                            <div class="col-9">
                                <h6 class="text-start">- approach to auditing wherein internal auditors make use of objective evidence in verifiying effectiveness of the processes.</h6>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
                </div>
            </div>
    </div>
    <div class="col-6">
        <h6 class="text-center">Criteria</h6>
    </div>
    <div class="col-6">
        <h6 class="text-center">Score</h6>
    </div>
    <div class="col-6">
        <h6>Auditing Principle</h6>
    </div>
    <div class="col-6">
        <div class="row">
            <div class="col-3 text-center"><b>1</b></div>
            <div class="col-3 text-center"><b>2</b></div>
            <div class="col-3 text-center"><b>3</b></div>
            <div class="col-3 text-center"><b>4</b></div>
        </div>
    </div>
    <div class="col-12">
        <br>
    </div>
    <div class="col-6 px-3 py-1">
        <h6>Ethical Conduct</h6>
    </div>
    <div class="col-6">
        <div class="row text-center">
            <div class="col-3">
                <input type="radio" name="ethical">
            </div>
            <div class="col-3">
                <input type="radio" name="ethical">
            </div>
            <div class="col-3">
                <input type="radio" name="ethical">
            </div>
            <div class="col-3">
                <input type="radio" name="ethical">
            </div>
        </div>
    </div>
    <div class="col-6  px-3 py-1">
        <h6>Fair Presentation</h6>
    </div>
    <div class="col-6">
        <div class="row text-center">
            <div class="col-3">
                <input type="radio" name="presentaton">
            </div>
            <div class="col-3">
                <input type="radio" name="presentaton">
            </div>
            <div class="col-3">
                <input type="radio" name="presentaton">
            </div>
            <div class="col-3">
                <input type="radio" name="presentaton">
            </div>
        </div>
    </div>
    <div class="col-6  px-3 py-1">
        <h6>Due Professional Care</h6>
    </div>
    <div class="col-6">
        <div class="row text-center">
            <div class="col-3">
                <input type="radio" name="care">
            </div>
            <div class="col-3">
                <input type="radio" name="care">
            </div>
            <div class="col-3">
                <input type="radio" name="care">
            </div>
            <div class="col-3">
                <input type="radio" name="care">
            </div>
        </div>
    </div>
    <div class="col-6  px-3 py-1">
        <h6>Independence</h6>
    </div>
    <div class="col-6">
        <div class="row text-center">
            <div class="col-3">
                <input type="radio" name="independence">
            </div>
            <div class="col-3">
                <input type="radio" name="independence">
            </div>
            <div class="col-3">
                <input type="radio" name="independence">
            </div>
            <div class="col-3">
                <input type="radio" name="independence">
            </div>
        </div>
    </div>
    <div class="col-6  px-3 py-1">
        <h6>Evidence-based Approach</h6>
    </div>
    <div class="col-6">
        <div class="row text-center">
            <div class="col-3">
                <input type="radio" name="approach">
            </div>
            <div class="col-3">
                <input type="radio" name="approach">
            </div>
            <div class="col-3">
                <input type="radio" name="approach">
            </div>
            <div class="col-3">
                <input type="radio" name="approach">
            </div>
        </div>
    </div>
    <div class="col-12">
        <h6>Comments on the Conducted Internal Audit</h6>
        <textarea class="form-control mb-2" name="" id="" cols="30" rows="10"></textarea>
    </div>
    <div class="col-12 text-center">
        <button type="button" class="btn btn-success mx-3">
            Confirm
            <i class="fas fa-check-circle"></i>
        </button>
    </div>
</div>
@endsection