@extends('layout.sidebar')
@section('title')
    <title>Survey Report</title>
@endsection
@section('css-page')
    <style>
        .btn-design {
            border: 1px solid #000000 !important;
            font-size: 1em !important;
        }

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

        .maxed{
            min-height: 16rem;
            max-height: 16rem;
        }
        .input-grade {
            width: 30px;
            text-align: center;
        }
    </style>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
@endsection


        @section('page')
            <div class="page-header pb-2">
                <h2>Survey Report</h2>
            </div>
            {{-- <div class="container pt-2"> --}}
            <div class="m-4">
                <div class="row g-3">
                    <form action="{{ route('hr-survey-report') }}">
                        @csrf
                        <div class="input-group mb-3 col-md-6">
                            <input type="text" name="keyword" class="form-control" placeholder="Input Office..." aria-describedby="basic-addon2" value="{{ $keyword ?? '' }}">
                            <input type="date" name="date_from" class="form-control" value="{{ $date_from }}">
                            <input type="date" name="date_to" class="form-control" value="{{ $date_to }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary"><i class="fa fa-search"></i> Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            
                <div class="row" style="background-color: rgb(255 255 255 / 70%);">
                    <div class="col-md-6">
                        <canvas id="pieChart" style="width:100%"></canvas>
                    </div>
                    <div class="col-md-6">
                        <canvas id="barChart" style="width:100%"></canvas>
                    </div>
                </div>
            
                <div class="row mt-2 ">
                        <div class="card p-3" style="background-color: rgb(255 255 255 / 70%);">
                            <div class="card-body pt-2 pb-5">
                                <h4>Associate</h4>
                                <hr>
                                <p class="text-success"><strong>Support: 50%, Confidence 50%</strong></p>
                                <div class="mt-3" >
                                    <form action="#" id="associateForm">
                                        <label>Office</label>
                                        <select name="facility" id="facility" class="form-control" required>
                                            <option value="">Select Office</option>
                                            @foreach($offices as $facility)
                                                <option value="{{ $facility->name }}">{{ $facility->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="btn btn-success mt-2 btn-associate">Associate</button>
                                    </form>
                                </div>
                                <div class="row" >
                                    <div class="col-12">
                                        <div class="mt-4 loader text-info text-center d-none">
                                            <i class="fa fa-cog fa-4x fa-spin"></i>
                                            <h3 class="mt-2">Loading...</h3>
                                        </div>
                                        <div id="noResult" class="mt-4 text-warning text-center d-none">
                                            <i class="fa fa-info-circle fa-4x"></i>
                                            <h3 class="mt-2">No result found.</h3>
                                        </div>
                                        <canvas id="pieChartApriori" class="d-none"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>
        @endsection
        
@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        var xValues = {!! json_encode($types->pluck('name')) !!};
        var yValues =  {!! json_encode($types->pluck('total')) !!};
        var barColors = {!! json_encode($types->pluck('color')) !!};

        new Chart("pieChart", {
            type: "pie",
            data: {
                labels: xValues,
                datasets: [{
                    backgroundColor: barColors,
                    data: yValues
                }]
            },
            options: {
                title: {
                display: true,
                text: "Survey Respondents \nTotal of {{ count($surveys)}}"
                }
            }
        });

        var xValues = {!! json_encode($facilities->pluck('name')) !!};
        var yValues =  {!! json_encode($facilities->pluck('total')) !!};
        var barColors = {!! json_encode($facilities->pluck('color')) !!};

        new Chart("barChart", {
        type: "bar",
        data: {
            labels: xValues,
            datasets: [{
                backgroundColor: barColors,
                data: yValues
            }]
        },
        options: {
            legend: {display: false},
            title: {
            display: true,
            text: "Survey Office \nTotal of {{ count($surveys)}}"
            }
        }
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
            
        $('#associateForm').on('submit', function(e){
            e.preventDefault();
            var facility = $('#facility').val();
            $('#pieChartApriori').addClass('d-none');
            $('#noResult').addClass('d-none');
            $('.loader').removeClass('d-none');
            window.scrollTo(0, document.body.scrollHeight);
            if(facility !== '') {
                $.ajax({
                    url : "{{ route('hr-survey-apriori') }}?facility=" + facility,
                    type: 'GET',
                    success: function(data) {
                        $('.loader').addClass('d-none');
                        if(data.facilities && data.facilities.length > 0) {
                            new Chart("pieChartApriori", {
                                type: "pie",
                                data: {
                                    labels: data.facilities,
                                    datasets: [{
                                        backgroundColor: data.colors,
                                        data: data.total
                                    }]
                                },
                                options: {
                                    title: {
                                        display: true,
                                        text: "Survey Respondents \nTotal of " + data.total_survey
                                    }
                                }
                            });
                            $('#pieChartApriori').removeClass('d-none');
                        }else{
                            $('#noResult').removeClass('d-none');
                        }
                        window.scrollTo(0, document.body.scrollHeight);
                    }
                });
            }
        });
    </script>
@endsection
