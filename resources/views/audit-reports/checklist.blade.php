@extends('layout.sidebar')
@section('title')
<title>Audit Checklist</title>
@endsection
@section('page')
<div class="page-header">
    <h1>Audit Checklist</h1>
</div>
<div class="row m-3">
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">Date Prepared</label>
            <input type="date" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">Date of Audit</label>
            <input type="date" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-12">
        <div class="m-2">
            <label for="auditor">Department/Process Area</label>
            <input type="text" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-12">
        <div class="m-2">
            <label for="auditor">Document Reference/ISO Clause</label>
            <input type="text" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">Auditor</label>
            <input type="text" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-md-6">
        <div class="m-2">
            <label for="auditor">Auditee</label>
            <input type="text" class="form-control" name="" id="">
        </div>
    </div>
    <div class="col-12 text-end">
        <button type="button" class="btn btn-success px-3" data-bs-toggle="modal" data-bs-target="#exampleModal"><i class="fas fa-info"></i></button>
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-start">**Reminder: This checklist is just a guide, you are free (and encouraged) to add more questions as you conduct the actual detail</h6>
                            </div>
                            <div class="col-12">
                                <h6 class="text-start">**Note to the auditor: Please ensure to check status of open corrective/preventive actions from previous internal audit(s). You have the option to close-out the open item if you find that the action(s) taken have been implemented or are effective already.</h6>
                            </div>
                            <div class="col-12">
                                <h6 class="text-start">**Check the following:</h6>
                            </div>
                            <div class="col-12">
                                <h6 class="text-start">The procedure is followed.</h6>
                            </div>
                            <div class="col-12">
                                <h6 class="text-start">The forms are completely filled.</h6>
                            </div>
                            <div class="col-12">
                                <h6 class="text-start">The records have complete signatures of concerned personnel.</h6>
                            </div>
                            <div class="col-12">
                                <h6 class="text-start">The filling of records generated.</h6>
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
    <div class="col-12">
        <table class="table table-bordered border-dark">
            <thead>
                <tr>
                    <th scope="col" class="text-center">Audit Trail</th>
                    <th scope="col" class="text-center">Comply(Y/N)</th>
                    <th scope="col" class="text-center">Audit Findings/Notes/Remarks (evidence)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td contenteditable="true"></td>
                    <td contenteditable="true"></td>
                    <td contenteditable="true"></td>
                </tr>
            </tbody>
        </table>
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