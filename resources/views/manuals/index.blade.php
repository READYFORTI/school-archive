@extends('layout.sidebar')
@section('title')
    <title>{{ $title ?? 'All Manuals'}}</title>
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
    </style>
@endsection
@section('page')
<div class="page-header pb-2">
    <h2>{{ $title ?? 'All Manuals'}}</h2>
</div>
<div class="container">
    <div class="row mt-2">
        <div class="col-12 px-2">
            <div class="card p-3">
                <div class="card-body pt-2">
                    <h4>{{ $title ?? 'All Manuals'}}</h4>
                    @include('layout.alert')
                    <table class="table datatables">
                        <thead><tr><td>#</td><td>Name</td><td>Type</td><td>Submitted By</td><td>Date</td><td>Status</td><td width="250px">Action</td></tr></thead>
                        <div style="max-height:400px; overflow-y:scroll">
                            <tbody>
                                @foreach($manuals as $manual)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $manual->name }}</td>
                                        <td>{{ empty($manual->parent_manual_id) ? 'Process Manual' : 'Process Manual - Updates' }}</td>
                                        <td>{{ sprintf('%s %s', $manual->user->firstname ?? '', $manual->user->surname ?? '') }}</td>
                                        <td>{{ $manual->date ? Carbon\Carbon::parse($manual->date)->format('M d, Y') : '' }}</td>
                                        <td>{{ ucwords(str_replace('-', ' ', $manual->status ?? 'pending')) }}</td>
                                        <td>
                                            <a href="{{ route('archives-show-file', $manual->file_id) }}" target="_blank" class="text-decoration-none px-1"><i class="fa fa-eye"></i> View</a>
                                            @if(!in_array($manual->status,['approved', 'rejected']))
                                                <a href="#" 
                                                    class="text-success btn-confirm px-1" 
                                                    data-target="#approve-manual-{{ $manual->id }}" 
                                                    data-message="Are you sure you wan't to approve this process manuals?">
                                                        <i class="fa fa-check"></i> Approve
                                                </a>

                                                <a href="#" class="text-danger btn-confirm px-1" 
                                                    data-target="#reject-manual-{{ $manual->id }}"
                                                    data-message="Are you sure you wan't to reject this process manuals?">
                                                        <i class="fa fa-ban"></i> Reject
                                                </a>

                                                <form action="{{ route('process-manuals.approve', $manual->id) }}" method="POST" id="approve-manual-{{ $manual->id }}">
                                                    @csrf
                                                    @method('PATCH')
                                                </form>
                                                
                                                <form action="{{ route('process-manuals.reject', $manual->id) }}" method="POST" id="reject-manual-{{ $manual->id }}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $('.btn-confirm').on('click', function(){
        var form = $(this).data('target');
        var message = $(this).data('message') ?? "Are you sure you wan't save changes?";
        Swal.fire({
            text: message,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(form).submit();
                }
        });
    });
});
</script>

@endsection