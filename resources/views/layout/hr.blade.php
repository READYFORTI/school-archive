<li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('user.dashboard') ? 'active' : '' }}" href="{{ route('user.dashboard') }}"><i class="fas fa-tachometer-alt mx-3 fa-lg"></i><span class="text-nowrap mx-2">Dashboard</span></a></li>
<li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('templates') ? 'active' : '' }}" href="{{ route('templates') }}"><i class="fas fa-newspaper mx-3 fa-lg"></i><span class="text-nowrap mx-2">Templates</span></a></li>
<li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->routeIs('hr-offices-page') ? 'active' : '' }}" href="{{ route('hr-offices-page') }}"><i class="fas fa-building mx-3 fa-lg"></i><span class="text-nowrap mx-2">Offices</span></a></li>  
<li class="nav-item dropdown {{ request()->is('survey/*') || request()->is('survey') ? 'show' : '' }}">
  <a data-bs-auto-close="false" class="dropdown-toggle nav-link text-start py-1 px-0 position-relative {{ request()->is('survey/*') || request()->is('survey') ? 'active' : '' }}" 
      aria-expanded="true" data-bs-toggle="dropdown" href="#"><i class="fas fa-chart-bar mx-3 fa-lg"></i>
      <span class="text-nowrap mx-2">Survey</span><i class="fas fa-caret-down float-none float-lg-end me-3"></i></a>
      <div class="dropdown-menu drop-menu border-0 animated fadeIn {{ request()->is('hr/survey/*') || request()->is('hr/survey') ? 'show' : '' }}" data-bs-popper="none">
          <a class="dropdown-item {{ request()->routeIs('hr-survey-page') ? 'active' : '' }}" href="{{ route('hr-survey-page') }}"><span>List</span></a>
          <a class="dropdown-item {{ request()->routeIs('hr-survey-report') ? 'active' : '' }}" href="{{ route('hr-survey-report') }}"><span>Reports</span></a>
      </div>
</li>
<li class="nav-item dropdown {{ request()->routeIs('hr.survey_report.index') || request()->routeIs('hr.survey_report.create') ? 'show' : '' }}">
  <a data-bs-auto-close="false" class="dropdown-toggle nav-link text-start py-1 px-0 position-relative {{ request()->routeIs('hr.survey_report.index') || request()->routeIs('hr.survey_report.create') ? 'active' : '' }}" 
      aria-expanded="true" data-bs-toggle="dropdown" href="#"><i class="fas fa-user-alt mx-3 fa-lg"></i>
      <span class="text-nowrap mx-2">Survey Reports</span><i class="fas fa-caret-down float-none float-lg-end me-3"></i></a>
      <div class="dropdown-menu drop-menu border-0 animated fadeIn {{ request()->routeIs('hr.survey_report.index') || request()->routeIs('hr.survey_report.create') ? 'show' : '' }}" data-bs-popper="none">
          <a class="dropdown-item {{ request()->routeIs('hr.survey_report.create') ? 'active' : '' }}" href="{{ route('hr.survey_report.create') }}"><span>Submit New Report</span></a>
          <a class="dropdown-item {{ request()->routeIs('hr.survey_report.index') ? 'active' : '' }}" href="{{ route('hr.survey_report.index') }}"><span>Survey Reports</span></a>
      </div>
</li>
<li class="nav-item"><a class="nav-link text-start py-1 px-0 {{ request()->is('archives*') ? 'active' : '' }}" href="{{ route('archives-page') }}"><i class="fas fa-archive mx-3 fa-lg"></i><span class="text-nowrap mx-2">Archive</span></a></li>
