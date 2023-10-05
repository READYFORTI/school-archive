<ul class="dropdown-menu text-left px-3">
    @if(!request()->routeIs('archives-show-file'))
        <li><a href="{{ route('archives-show-file', $file->id) }}" target="_blank" class="text-decoration-none"><i class="fa fa-eye"></i> Open</a></li>
    @endif
    @if(!empty($file->trackings()))
    <li>
        <a href="#" class="text-decoration-none btn-tracking"
            data-bs-toggle="modal" data-bs-target="#trackingModal"
        ><i class="fa fa-search"></i> Track</a>
        <div class="d-none file-tracking-info">
            <div class="px-5 mb-4 text-center">
                Name: {{ $file->file_name }}<br/>
                Uploaded By: {{ $file->user->username }}<br/>
                Uploaded At: {{ $file->created_at->format('F d, Y h:i A') }}
            </div>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <td>User Type</td>
                        <td>Name</td>
                        <td>Date</td>
                    </tr>
                </thead>
                <tbody>
                    @foreach($file->trackings() as $track)
                        <tr class="">
                            <td><i class="fa fa-user"></i> {{ $track['name'] ?? '' }}</td>
                            <td>{{ !empty($track['user']) ? "By: ". $track['user'] : '  ' }}</td>
                            <td>{{ !empty($track['date']) ? "Date: ".$track['date'] : '  ' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- <div class="tracking-container">
                @foreach($file->trackings() as $track)
                    <div class="tracking-item">
                        <span><strong>{{ $track['name'] ?? '' }}</strong></span><br/>
                        <div class="pt-2 item-box text-white {{ $track['color'] ?? 'bg-secondary' }}">
                            <i class="fa fa-user"></i>
                        </div>
                    <small>&nbsp;{{ !empty($track['user']) ? "By: ". $track['user'] : ' ' }}</small><br/>
                        <small>&nbsp;{{ !empty($track['date']) ? "Date: ".$track['date'] : ' ' }}</small>
                    </div>
                @endforeach
            </div> -->
        </div>
    </li>
    @endif
    @if(Auth::user()->role->role_name == 'Internal Auditor' 
        && $file->user_id == Auth::user()->id
        && $file->type == 'audit_reports'
        && !empty($file->audit_report)
        && empty($file->audit_report->cars))
        <a href="#" class="text-decoration-none upload-cars" data-audit-report="{{ $file->audit_report->id ?? '' }}" data-bs-toggle="modal" data-bs-target="#consolAuditReportModal"><i class="fa fa-book"></i> Upload CARS</a>
    @endif
    <!-- @if($file->user_id == Auth::user()->id)
    <li>
        <a href="#" class="text-decoration-none btn-share" data-bs-toggle="modal" data-bs-target="#shareModal" data-users="{{ $file->shared_users }}" data-route="{{ route('archives-share-file', $file->id) }}"><i class="fa fa-share"></i> Share</button>
            <form id="unshare_file_{{ $file->id }}" action="{{ route('archives-unshare-file', $file->id) }}" class="d-none" method="POST">
                @csrf
            </form>
        </a>
    </li>
    @endif -->
    @if($file->user_id == Auth::user()->id)
        <li>
            <a href="#" class="text-decoration-none btn-edit-file"
                data-bs-toggle="modal" data-bs-target="#editFileModal"
                data-route="{{ route('archives-update-file', $file->id) }}"
                data-name="{{ $file->file_name }}"
                data-description="{{ $file->description ?? ''}}"
            ><i class="fa fa-edit"></i> Edit</a>
        </li>
    @endif
    <li>
        <a href="#" class="text-decoration-none btn-history"
            data-file-id="{{ $file->id }}"
            data-bs-toggle="modal" data-bs-target="#historyModal"
        ><i class="fa fa-history"></i> History</a>
        
    </li>
    @if($file->user_id == Auth::user()->id)
    <li>
        <a href="#" class="text-decoration-none btn-confirm" data-message="Are you sure you wan't to delete this file?" data-target="#delete_file_{{ $file->id }}"><i class="fa fa-trash"></i> Delete</button>
            <form id="delete_file_{{ $file->id }}" action="{{ route('archives-delete-file', $file->id) }}" class="d-none" method="POST">
                @csrf
                @method('DELETE')
            </form>
        </a>
    </li>
    @endif
    @if(!empty($file->manual) && $file->manual->status != 'approved' && 
        in_array(Auth::user()->role->role_name, ['Quality Assurance Director', 'Administrator', 'College Management Team'])
    )
    <li>
        <a href="#" 
            class="text-decoration btn-confirm" 
            data-target="#approve-manual-{{ $file->manual->id }}" 
            data-message="Are you sure you wan't to approve this process manuals?">
                <i class="fa fa-check"></i> Approve Manuals
        </a>
        <form action="{{ route('process-manuals.approve', $file->manual->id) }}" method="POST" id="approve-manual-{{ $file->manual->id }}">
            @csrf
            @method('PATCH')
        </form>
    </li>
    <li>
        <a href="#" class="text-decoration btn-confirm" 
            data-target="#reject-manual-{{ $file->manual->id }}"
            data-message="Are you sure you wan't to reject this process manuals?">
                <i class="fa fa-ban"></i> Reject Manuals
        </a>
        <form action="{{ route('process-manuals.reject', $file->manual->id) }}" method="POST" id="reject-manual-{{ $file->manual->id }}">
            @csrf
            @method('DELETE')
        </form>
    </li>
    @endif
</ul>