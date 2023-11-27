</div><!DOCTYPE html>
<html lang="en">
<head>
  <!-- Include necessary Bootstrap CSS and other dependencies -->
  <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
  
</head>

<body>
@extends('layout.sidebar')
@section('title')
<title>Add Audit Plan</title>
@endsection

@section('page')
    <div class="page-header">
        <h2>Add Audit Plan</h2>
    </div>
    <div class="container-fluid ">
    <div class="row">
        <div class="col-lg-9 p-3">
            <div class="m-3 bg-white py-2">
                <div class="px-3 py-2">
                    @include('layout.alert')
                    <form id="auditPlanForm" method="POST" action="{{ route('lead-auditor.audit.save') }}">
                        @csrf
                        <div>
                            <div class="mb-3">
                                <select class="form-select" id="audit_plan_name" name="name" required>
                                    <option class="1st Half">1st Half</option>
                                    <option class="2nd Half">2nd Half</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="audit_plan_description" class="form-label">Description</label><i class="text-danger"> *</i>
                                <textarea class="form-control shadow-none" rows="3" id="audit_plan_description" name="description" placeholder="Enter description" required></textarea>
                            </div><br>
        
                            <div class="mt-4">
                                <h4>Process and Auditors</h4>
                                <button class="btn btn-success float-end btn-process-modal" type="button" data-bs-toggle="modal" data-bs-target="#addProcessModal"><i class="fa fa-plus"></i> Add Process</button><br><br>
                                <table class="table text-black table-process mt-3">
                                    <thead>
                                        <tr>
                                            <th>PROCESS</th>
                                            <th>AUDITORS</th>
                                            <th>Date</th>
                                            <th>From</th>
                                            <th>TO</th>
                                            <th><i class="fas fa-cogs"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
        
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-success btn-save px-4 py-2"><i class="fa fa-save"></i> Save Audit Plan</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
    
        <div class="col-lg-3 p-3">
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
    </div>
    


    <div class="modal fade" id="addProcessModal" tabindex="-1" aria-labelledby="addProcessModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Process And Auditors</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                    <div class="modal-body">
                        <div class="mb-3 text-black">
                            <label for="name" class="form-label">Select Process</label>
                            <div class="tree"></div>
                        </div>
                        <div class="mb-3 auditors-panel">
                            <label for="name" class="form-label">Auditors</label>
                            <select required id="auditors" class="form-control select2" multiple required data-placeholder="Choose Auditors" required>
                                @foreach($auditors as $user)
                                    <option value="{{ $user->id }}">{{ sprintf("%s %s", $user->firstname ?? '', $user->surname ?? '') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-lable">Date</label>
                            <input type="date" id="date_selected" class="form-control" name="date[]" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-lable">From</label>
                            <input type="time" id="from_time" class="form-control" name="from_time[]" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-lable">To</label>
                            <input type="time" id="to_time" class="form-control" name="to_time[]" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-add-process"><i class="fa fa-plus"></i> Add</button>
                        <button type="button" class="btn btn-close-modal btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

      <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    </body>
    </html>
@endsection

@section('js')
<script src="{{ asset('packages/bootstrap-treeview-1.2.0/src/js/bootstrap-treeview.js') }}"></script>
<script>
    
    var areas = {!! json_encode($tree_areas) !!};
    var main = {!! json_encode($main) !!};

    console.log(areas);
    console.log(main);

    var tree = $('.tree').treeview({
        data: main,
        multiSelect: true,
        collapseIcon: "fa fa-minus",
        expandIcon: "fa fa-plus",
        onNodeSelected: function(event, data) {
            var processName = data.text;
            var unselectedNodes = tree.treeview('getUnselected');
            unselectedNodes.forEach(element => {
                if(processName == element.text) {
                    tree.treeview('selectNode', [ element.nodeId, { silent: true } ]);
                }
            });
        }
    });

    $('.btn-process-modal').on('click', function(){
        var selectedNodes = tree.treeview('getSelected');
        selectedNodes.forEach(element => {
            tree.treeview('unselectNode', [ element.nodeId, { silent: true } ]);
        });
    });
    

    tree.treeview('expandAll', { levels: 1});

    $('.select2').select2({
        'width': '100%',
        dropdownParent: $('.auditors-panel')
    });

    $("#audit_plan_date").flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
    });

    

    $('.btn-save').on('click', function(e){
        if($('.table-process tbody > tr').length == 0) {
            e.preventDefault();
            Swal.fire({
                text: 'Please Add Process...',
                icon: 'warning',
            });
        }
    });

    $('.btn-add-process').on('click', function() {
        
        var auditors_name = '';
        var auditors_id = '';
        $('#auditors option:selected').each(function(i, val){
            auditors_name += val.text;
            auditors_id += val.value;
            if(i <  ($('#auditors option:selected').length -1)) {
                auditors_name += ', ';
                auditors_id += ',';
            }
        });

        let date_selected = $('#date_selected').val();
        let from_time = $('#from_time').val();
        let to_time = $('#to_time').val();

        var selected = tree.treeview('getSelected');
        var area_names = '';
        var area_ids = '';
        var i = 0;
        selected.forEach(function(area) {
            var parent = tree.treeview('getNode', area.parentId);
            area_names += parent.text + ` > ` + area.text;
            area_ids += area.id;
            if(i < selected.length - 1) {
                area_names += ', ';
                area_ids += ',';
                i++;
            }
        });

        $('.table-process tbody').append(`<tr>
                <td>` + area_names + `</td>
                <td>` + auditors_name + `</td>
                <td>` + date_selected + `</td>
                <td>` + from_time + `</td>
                <td>` + to_time + `</td>
                <td>
                    <button class="btn btn-danger btn-remove" type="button"><i class="fa fa-times"></i></button>
                    <input type="hidden" name="area_names[]" value="` + area_names + `">
                    <input type="hidden" name="process[]" value="` + area_ids + `">
                    <input type="hidden" name="auditors[]" value="` + auditors_id + `">
                    <input type="hidden" name="date_selected[]" value="` + date_selected + `">
                    <input type="hidden" name="from_time[]" value="` + from_time + `">
                    <input type="hidden" name="to_time[]" value="` + to_time + `">
                </td>
        </tr>`);

        $('.btn-close-modal').trigger('click');
    });

    $(document).on('click','.btn-remove', function(){
        $(this).parents('tr').remove();
    });
</script>
@endsection