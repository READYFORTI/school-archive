@extends('layout.sidebar')
@section('title')
    <title>Area</title>
@endsection
@section('css-page')
    <style>
        .btn-design:hover{
            color: #ffffff !important;
            background-color: #005b40 !important;
        }

        .row .col-4 .active{
            color: #ffffff !important;
            background-color: #005b40 !important;
        }

        .row .col-8 .active{
            color: #ffffff !important;
            background-color: #005b40 !important;
        }
    </style>
@endsection
@section('page')
    <div class="mt-3 px-3">
        <!-- <h1>Areas</h1> -->
        <div class="row">
            <div class="col-8">
                @foreach ($main_areas as $row)
                    <button type="button" class="btn btn-design btn-main-area me-2 {{ $loop->index == 0 ? 'active' : ''}}" data-value="{{ $row->id }}"><span class="mdi mdi-domain"></span> {{ $row->area_name }}</button>
                @endforeach
            </div>
            <div class="col-4 d-flex align-items-center justify-content-end">
                <div class="dropdown">
                    <button type="button" class="btn btn-success" data-bs-toggle="dropdown" aria-expanded="false"><span
                            class="mdi mdi-plus"></span> Add</button>
                    <ul class="dropdown-menu">
                        <li><button class="dropdown-item text-success btn-add" data-type="office" data-bs-toggle="modal" data-bs-target="#areaModal"><span class="mdi mdi-home-account text-success"></span>
                                Office</button></li>
                        <li><button class="dropdown-item text-success btn-add" data-type="institute" data-bs-toggle="modal" data-bs-target="#areaModal"><span
                                    class="mdi mdi-domain text-success"></span>
                                Institute</button></li>
                        <li><button class="dropdown-item text-success btn-add" data-type="program" data-bs-toggle="modal" data-bs-target="#areaModal"><span class="mdi mdi-office-building text-success"></span>
                                Program</button></li>
                        <li><button class="dropdown-item text-success btn-add" data-type="process" data-bs-toggle="modal" data-bs-target="#areaModal"><span class="mdi mdi-folder-table text-success"></span>
                                Process</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <hr width="100%">

    {{-- Transaction Messages --}}
    <div style="min-height: 20vh">
        <div class="m-3">
            @include('layout.alert')
            <div class="col-8 mt-3 row area-container">
                
            </div>

            <div class="col-8 mt-3 row program-container">

            </div>

            <div class="col-8 mt-3 row process-container">

            </div>
        </div>
    </div>

    <!-- Office Modal -->
    <div class="modal fade" id="areaModal" tabindex="-1" aria-labelledby="areaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-capitalize" id="areaModalLabel">Add Office</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="" id="areaForm">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="area_id" id="area_id">
                        <input type="hidden" name="area_type" id="area_type">
                        <div class="select-container"></div>
                        <div class="mb-3">
                            <label for="process" class="form-label">Name</label>
                            <input type="text" class="form-control" id="area_name" name="area_name" placeholder="Enter name" required>
                        </div>
                        <div class="mb-3">
                            <label for="process" class="form-label">Description</label>
                            <textarea class="form-control" rows="3" id="area_description" name="area_description" placeholder="Enter description" required></textarea>
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

    <script>
        var areas = {!! json_encode($areas) !!};
        var main_areas = {!! json_encode($main_areas) !!};

        document.addEventListener('DOMContentLoaded', function() {
            var area_container = $('.area-container');
            var sub_area_container = $('.sub-area-container');
            var process_container = $('.process-container');
            var program_container = $('.program-container');

            function loadArea(area_id) {
                area_id = parseInt(area_id);
                var area = main_areas.find(item => item.id === area_id);
                area_container.html('');
                sub_area_container.html('');

                var child_areas = areas.filter(i => i.parent_area == area_id);
                // if(child_areas.length > 0) {
                //     var type = area.area_name == 'Administration' ? 'Offices' : 'Institute';
                //     area_container.html('<h2 class="my-3">' + type + '</h2>');

                //     child_areas.forEach(function(i){
                //         area_container.append(`<div class="col-2 text-center">
                //             <button class=" pt-3 btn align-items-center justify-content-center btn-sub-area" data-area-id="` + i.id + `" data-bs-toggle="dropdown" aria-expanded="false">
                //                 <i class="fa fa-building fa-2x text-success"></i>
                //                 <p style="text-overflow: ellipsis"><small>` +  i.area_name + `</small></p>
                //             </button>
                //             <ul class="dropdown-menu text-left">
                //                 <li><button type="button" class="dropdown-item btn-edit" data-type="` + i.type + `" data-area-id="` + i.id + `" data-bs-toggle="modal" data-bs-target="#areaModal">Edit</button></li>
                //             </ul>
                //         </div>`);
                //     });
                // }
                if (child_areas.length > 0) 
                {
                    var type = area.area_name == 'Administration' ? 'Offices' : 'Institute';
                    // area_container.html('<h2 class="my-3 text-white">' + type + '</h2>');

                    child_areas.forEach(function (i) {
                        area_container.append(`
                        <div class="col-md-2 col-sm-4 col-6 mb-4 text-center" > <!-- Adjust the column widths based on your requirements -->
                            <button class="pt-3 btn align-items-center justify-content-center btn-sub-area" data-area-id="` + i.id + `" data-bs-toggle="dropdown" aria-expanded="false" style="border: none">
                            <i class="fa fa-building fa-4x text-success"></i> <!-- Increased the icon size to 3x -->
                            <p style="text-overflow: ellipsis; font-size: 14px;"><small>` + i.area_name + `</small></p> <!-- Increased the font size to 14px -->
                            </button>
                            <ul class="dropdown-menu text-left">
                            <li><button type="button" class="dropdown-item btn-edit " data-type="` + i.type + `" data-area-id="` + i.id + `" data-bs-toggle="modal" data-bs-target="#areaModal"><i class="fas fa-edit"></i> Edit</button></li>
                            </ul>
                        </div>
                        `);
                    });
                }


            }
            loadArea(main_areas[0].id);

            $('.btn-main-area').on('click', function(){
                $('.btn-main-area').removeClass('active');
                $(this).addClass('active');
                loadArea($(this).data('value'));
            });

            function loadSubArea(area_id) {
                area_id = parseInt(area_id);
                var area = areas.find(item => item.id === area_id);
                program_container.html('');
                process_container.html('');

                var program_areas = areas.filter(i => i.parent_area == area_id && i.type == 'program');
                if(program_areas.length > 0) {
                    program_container.html('<h2 class="my-3 text-success">Programs</h2>');

                    program_areas.forEach(function(i){
                        program_container.append(`<div class="col-2 text-center">
                            <button class="pt-3 btn align-items-center justify-content-center btn-sub-area" data-area-id="` + i.id + `" data-bs-toggle="dropdown" aria-expanded="false" style="border: none">
                                <i class="fa fa fa-book fa-2x text-success"></i>
                                <p style="text-overflow: ellipsis"><small>` + i.area_name + `</small></p>
                            </button>
                            <ul class="dropdown-menu text-left">
                                <li><button type="button" class="dropdown-item btn-edit" data-area-id="` + i.id + `" data-type="` + i.type + `" data-bs-toggle="modal" data-bs-target="#areaModal"><i class="fas fa-edit"></i> Edit</button></li>
                            </ul>
                        </div>`);
                    });
                }

                var process_areas = areas.filter(i => i.parent_area == area_id && i.type == 'process');
                if(process_areas.length > 0) {
                    process_container.html('<h2 class="my-3 text-success">Process</h2>');

                    process_areas.forEach(function(i){
                        process_container.append(`<div class="col-2 text-center">
                            <button class="pt-3 btn align-items-center justify-content-center btn-sub-area" data-area-id="` + i.id + `" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa fa-book fa-2x text-success"></i>
                                <p style="text-overflow: ellipsis"><small>` + i.area_name + `</small></p>
                            </button>
                            <ul class="dropdown-menu text-left">
                                <li><button type="button" class="dropdown-item btn-edit" data-area-id="` + i.id + `" data-type="` + i.type + `" data-bs-toggle="modal" data-bs-target="#areaModal">Edit</button></li>
                            </ul>
                        </div>`);
                    });
                }
            }
            $('.area-container').on('click', '.btn-sub-area', function(){
                $('.btn-sub-area').removeClass('active');
                $(this).addClass('active');
                loadSubArea($(this).data('area-id'));
            });

            $('.program-container').on('click', '.btn-sub-area', function(){
                area_id = parseInt($(this).data('area-id'));
                $('.btn-sub-area').removeClass('active');
                $(this).addClass('active');

                process_container.html('');
                var process_areas = areas.filter(i => i.parent_area == area_id && i.type == 'process');
                if(process_areas.length > 0) {
                    process_container.html('<h2 class="my-3 text-success">Process</h2>');

                    process_areas.forEach(function(i){
                        process_container.append(`<div class="col-2 text-center">
                            <button class="pt-3 btn align-items-center justify-content-center btn-sub-area" data-area-id="` + i.id + `" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa fa-book fa-2x text-success"></i>
                                <p style="text-overflow: ellipsis"><small>` + i.area_name + `</small></p>
                            </button>
                            <ul class="dropdown-menu text-left">
                                <li><button type="button" class="dropdown-item btn-edit" data-area-id="` + i.id + `" data-type="` + i.type + `" data-bs-toggle="modal" data-bs-target="#areaModal">Edit</button></li>
                            </ul>
                        </div>`);
                    });
                }
            });

            function initModal(type, area = null)
            {
                $('#area_id').val('');
                $('#area_type').val(type);
                $('#areaModalLabel').html('Add ' + type);
                $('.select-container').html('');
                $('#areaModal .form-control').val('');

                if(area) {
                    $('#areaModalLabel').html('Edit ' + type);
                    $('#area_id').val(area.id);
                    $('#area_name').val(area.area_name);
                    $('#area_description').html(area.area_description);
                }else{
                    if(type == 'process') {
                        $('.select-container').append(`<div class="mb-3">
                            <label for="main_area" class="form-label">Select Area</label>
                            <select class="form-control" id="main_area" required><option value="">Select Area</option></select>
                        </div>`);
                        main_areas.forEach(function(m){
                            $('#main_area').append(`<option value="` + m.id + `">` + m.area_name + `</option>`);
                        });
                        
                        $('.select-container').append('<div class="sub-select-container"></div>');
                    }
                    if(type == 'program') {
                        $('.select-container').append(`<div class="mb-3">
                            <label for="parent_area" class="form-label text-success">Institute</label>
                            <select class="form-control area-select" name="parent_area" required></select>
                        </div>`);
                        var parent_areas = areas.filter(i => i.type == 'institute');
                        parent_areas.forEach(function(i){
                            $('.area-select').append(`<option value="` + i.id + `">` + i.area_name + `</option`);
                        });
                    }
                }
            }
            $('.btn-add').on('click', function(){
                var type = $(this).data('type');
                $('#areaForm').prop('action', "{{ route('staff-area-store') }}");
                initModal(type);
            });

            $('div').on('click', '.btn-edit', function(){
                var area_id = $(this).data('area-id');
                var type = $(this).data('type');
                
                area_id = parseInt(area_id);
                var area = areas.find(item => item.id === area_id);
                $('#areaForm').prop('action', "{{ route('staff-area-update') }}");
                initModal(type, area);
            });

            $('.select-container').on('change', '#main_area', function(){
                area_id = parseInt($(this).val());
                var area = areas.find(item => item.id === area_id);
                $('.sub-select-container').html('');
                var label = area.area_name == 'Administration' ? 'Office' : 'Institute';

                if(label == 'Office') {
                    $('.sub-select-container').append(`<div class="mb-3">
                        <label for="parent_area" class="form-label">Select ` + label + `</label>
                        <select class="form-control sub-select" id="parent_area" name="parent_area" required><option value=''>Select Office</option></select>
                    </div>`);
                } else {
                    $('.sub-select-container').append(`<div class="mb-3">
                        <label for="institute" class="form-label">Select ` + label + `</label>
                        <select class="form-control sub-select" id="institute" name="institute" required><option value=''>Select Institute</option></select>
                    </div><div class="program-select">`);
                }

                var parent_areas = areas.filter(i => i.parent_area == area.id);
                parent_areas.forEach(function(i){
                    $('.sub-select-container .sub-select').append(`<option value="` + i.id + `">` + i.area_name + `</option`);
                });
            });

            $('.select-container').on('change', '#institute', function(){
                area_id = parseInt($(this).val());
                var area = areas.find(item => item.id === area_id);
                $('.program-select').html('');

                $('.program-select').append(`<div class="mb-3">
                    <label for="parent_area" class="form-label">Select Program</label>
                    <select class="form-control" id="parent_area" name="parent_area" required><option value=''>Select Program</option></select>
                </div>`);

                var parent_areas = areas.filter(i => i.parent_area == area.id);
                parent_areas.forEach(function(i){
                    $('#parent_area').append(`<option value="` + i.id + `">` + i.area_name + `</option`);
                });
            });
        });
    </script>
@endsection
