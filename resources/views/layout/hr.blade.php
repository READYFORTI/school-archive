<ul class="nav flex-column shadow d-flex sidebar mobile-hid">
  <li class="nav-item logo-holder">
      <div class="text-center text-white logo py-4 mx-4"><img class="img-fluid" src="{{ asset('storage/assets/dnsc-logo.png') }}" width="55" height="50"><a id="title" class="text-decoration-none" href="#"><strong>DNSC</strong></a><a class="float-end text-white" id="sidebarToggleHolder" href="#"><i class="fas fa-bars" id="sidebarToggle"></i></a></div>
  </li>


  <li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><i class="fas fa-tachometer-alt mx-3"></i><span class="text-nowrap mx-2">Dashboard</span></a></li>
  <li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('templates') ? 'active' : '' }}" href="{{ route('templates') }}"><i class="fas fa-newspaper mx-3"></i><span class="text-nowrap mx-2">Templates</span></a></li>
  <li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('hr-offices-page') ? 'active' : '' }}" href="{{ route('hr-offices-page') }}"><i class="fas fa-building mx-3"></i><span class="text-nowrap mx-2">Offices</span></a></li>  
  <li class="nav-item dropdown {{ request()->is('survey/*') || request()->is('survey') ? 'show' : '' }}">
    <a data-bs-auto-close="false" class="dropdown-toggle nav-link text-start py-1 px-0 position-relative {{ request()->is('survey/*') || request()->is('survey') ? 'active' : '' }}" 
        aria-expanded="true" data-bs-toggle="dropdown" href="#"><i class="fas fa-chart-bar mx-3"></i>
        <span class="text-nowrap mx-2">Survey</span></a>
        <div class="dropdown-menu drop-menu border-0 animated fadeIn {{ request()->is('hr/survey/*') || request()->is('hr/survey') ? 'show' : '' }}" data-bs-popper="none">
            <a class="dropdown-item {{ request()->routeIs('hr-survey-page') ? 'active' : '' }}" href="{{ route('hr-survey-page') }}"><span>List</span></a>
            <a class="dropdown-item {{ request()->routeIs('hr-survey-report') ? 'active' : '' }}" href="{{ route('hr-survey-report') }}"><span>Reports</span></a>
        </div>
  </li>
  <li class="nav-item dropdown {{ request()->routeIs('hr.survey_report.index') || request()->routeIs('hr.survey_report.create') ? 'show' : '' }}">
    <a data-bs-auto-close="false" class="dropdown-toggle nav-link text-start py-1 px-0 position-relative {{ request()->routeIs('hr.survey_report.index') || request()->routeIs('hr.survey_report.create') ? 'active' : '' }}" 
        aria-expanded="true" data-bs-toggle="dropdown" href="#"><i class="fas fa-user-alt mx-3"></i>
        <span class="text-nowrap mx-2">Survey Reports</span><i class="fas fa-caret-down float-none float-lg-end me-3"></i></a>
        <div class="dropdown-menu drop-menu border-0 animated fadeIn {{ request()->routeIs('hr.survey_report.index') || request()->routeIs('hr.survey_report.create') ? 'show' : '' }}" data-bs-popper="none">
            <a class="dropdown-item {{ request()->routeIs('hr.survey_report.create') ? 'active' : '' }}" href="{{ route('hr.survey_report.create') }}"><span>Submit New Report</span></a>
            <a class="dropdown-item {{ request()->routeIs('hr.survey_report.index') ? 'active' : '' }}" href="{{ route('hr.survey_report.index') }}"><span>Survey Reports</span></a>
        </div>
  </li>

  <!-- <li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('archives-shared') ? 'active' : '' }}" href="{{ route('archives-shared') }}"><i class="fas fa-share mx-3 mx-3"></i><span class="text-nowrap mx-2">Shared with me</span></a></li> -->
  <li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('archives-page') ? 'active' : '' }}" href="{{ route('archives-page') }}"><i class="fas fa-archive mx-3"></i><span class="text-nowrap mx-2">Archive</span></a></li>
</ul>

