<li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><i class="fas fa-tachometer-alt mx-3 fa-lg"></i><span class="text-nowrap mx-2">Dashboard</span></a></li>
<li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('templates') ? 'active' : '' }}" href="{{ route('templates') }}"><i class="fas fa-newspaper mx-3 fa-lg"></i><span class="text-nowrap mx-2">Controlled Forms</span></a></li>
<li class="nav-item dropdown {{ request()->is('process-manuals*') ? 'active' : '' }}">
    <a data-bs-auto-close="false" class="dropdown-toggle nav-link text-start py-1 px-0 position-relative" aria-expanded="true" data-bs-toggle="dropdown" href="#"><i class="fas fa-receipt mx-3 fa-lg"></i><span class="text-nowrap mx-2">Process Manuals</span><i class="fas fa-caret-down float-none float-lg-end me-3"></i></a>
    <div class="dropdown-menu drop-menu border-0 animated fadeIn {{ request()->is('process-manuals*') ? 'show' : '' }}" data-bs-popper="none">
        <a class="dropdown-item {{ request()->routeIs('process-manuals.pending') ? 'active' : '' }}" href="{{ route('process-manuals.pending') }}"><span>Pending</span></a>
        <a class="dropdown-item {{ request()->routeIs('process-manuals.pending-updates') ? 'active' : '' }}" href="{{ route('process-manuals.pending-updates') }}"><span>Pending Updates</span></a>
        <a class="dropdown-item {{ request()->routeIs('process-manuals.all') ? 'active' : '' }}" href="{{ route('process-manuals.all') }}"><span>All Process Manuals</span></a>
    </div>
</li>
<li class="nav-item">
    <a class="nav-link text-start py-1 px-0 {{ request()->routeIs('cmt.survey-reports') ? 'active' : '' }}" href="{{ route('cmt.survey-reports') }}">
        <i class="fas fa-book mx-3 mx-3 mx-3 fa-lg"></i><span class="text-nowrap mx-2">Pending SR</span>
    </a>
</li>
<li class="nav-item">
    <a class="nav-link text-start py-1 px-0 {{ request()->routeIs('cmt.consolidated-audit-reports') ? 'active' : '' }}" href="{{ route('cmt.consolidated-audit-reports') }}">
        <i class="fas fa-receipt mx-3 mx-3 mx-3 fa-lg"></i><span class="text-nowrap mx-2">Pending CR</span>
    </a>
</li>
<!-- <li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('archives-shared') ? 'active' : '' }}" href="{{ route('archives-shared') }}"><i class="fas fa-share mx-3 mx-3"></i><span class="text-nowrap mx-2">Shared with me</span></a></li> -->
<li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->is('archives*') ? 'active' : '' }}" href="{{ route('archives-page') }}"><i class="fas fa-archive mx-3 fa-lg"></i><span class="text-nowrap mx-2">Archive</span></a></li>

