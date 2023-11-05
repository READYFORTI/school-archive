<div class="modal fade" id="propertyModal" tabindex="-1" aria-labelledby="propertyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Properties</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    <table class="table">
                        <tr><td><strong>Name:</strong></td><td id="propertyName"></td></tr>
                        <tr><td><strong>Type:</strong></td><td id="propertyType"></td></tr>
                        <tr><td><strong>Full Path:</strong></td><td id="propertyFullPath"></td></tr>
                        <tr><td><strong>Created By:</strong></td><td id="propertyCreatedBy"></td></tr>
                        <tr><td><strong>Created:</strong></td><td id="propertyCreated"></td></tr>
                        <tr><td><strong>Updated:</strong></td><td id="propertyUpdated"></td></tr>
                        <tr><td colspan="2"><strong>Description:</strong></td></tr>
                        <tr>
                            <td colspan="2">
                                <textarea class="form-control" row="5" readonly id="propertyDescription"></textarea>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="filePropertyModal" tabindex="-1" aria-labelledby="filePropertyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Properties</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    <div class="file-history">
                        <h5>File History</h5>
                        <table class="table file-history-table">
                            <thead>
                                <tr>
                                    <td>Name</td>
                                    <td>Description</td>
                                    <td>Date</td>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="trackingModal" tabindex="-1" aria-labelledby="trackingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tracking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!empty($users))
<div class="modal fade" id="shareModal" tabindex="-1" aria-labelledby="shareModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Share File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="#" id="shareModalForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="search" class="form-label">Share File With:</label>
                        <select class="form-control" name="userShare[]" id="userShare" multiple>
                            @foreach($users as $user)
                                @if($user->id !== $current_user->id)
                                    <option value="{{ $user->id }}">{{ sprintf("%s %s - %s", $user->firstname ?? '', $user->surname ?? '', $user->role->role_name ?? '') }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-share"></i> Share</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="remarksModal" tabindex="-1" aria-labelledby="remarksModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Remarks</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="" id="remarksForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="remarksDetailForm">
                            <div class="col-12 mb-3">
                                <label class="form-label">Choose Remarks:</label><br/>
                                <input type="radio" class="btn-check" name="type" id="remarks-success" value="success" autocomplete="off" checked>
                                <label class="btn btn-outline-success p-2 px-4" for="remarks-success"></label>

                                <input type="radio" class="btn-check" name="type" id="remarks-danger" value="danger" autocomplete="off">
                                <label class="btn btn-outline-danger p-2 px-4" for="remarks-danger"></label>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="comments">Comments:</label>
                                <textarea class="form-control" rows="3" name="comments" id="remarks-comments"></textarea>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="comments">Recent Remarks:</label>
                            <table class="table recent-remarks-table"></table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success btn-submit-remarks">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="consolAuditReportModal" tabindex="-1" aria-labelledby="consolAuditReportModalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="consolAuditReportModalModalLabel">Upload CARS</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('auditor.cars.store') }}" enctype="multipart/form-data" id="fileModalForm">
                @csrf
                <input type="hidden" value="" id="audit_report_id" name="audit_report_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Filename" required>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date:</label>
                        <input type="date" id="date" class="form-control" name="date" max="{{ date('Y-m-d') }}"/>
                    </div>
                    <div class="mb-3">
                        <label for="search" class="form-label">Description:</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file_attachments" class="form-label">Attachment</label>
                        <input type="file" class="form-control" name="file_attachments[]" 
                            id="file_attachments" required multiple
                            accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editFileModal" tabindex="-1" aria-labelledby="editFileModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="#" id="updateFileModalForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_file_name" class="form-label">Name</label>
                        <input type="text" class="form-control" name="file_name" id="edit_file_name" placeholder="Enter File Name" required>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Description</label>
                        <textarea class="form-control" name="file_description" id="file_description" placeholder="Enter File Description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="file_attachments" class="form-label">Attachment</label>
                        <input type="file" class="form-control" name="file_attachments[]" id="file_attachments" required multiple accept="image/jpeg,image/png,application/pdf,application/vnd.oasis.opendocument.text,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>