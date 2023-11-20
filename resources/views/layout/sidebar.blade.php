@extends('layout.app')

@section('css')
<style>
    body {   
        background: url('{{ asset("/media/awts.jpg") }}') no-repeat center center fixed; 
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
    }

    .container {
        background: rgb(15 15 15 / 40%);
        padding-top: 30px;
        padding-right: 30px;
        padding: 30px;
        margin-left: 18px !important;
        width: 100%;
        max-width: 98%;
        border-radius: 20px;
        margin-top: 20px;
        color: #ffff;
    }

    @media (min-width: 992px) {
        body {
            padding-left: 14rem;
        }
    }

    .drop-menu .active{
        background-color: #ffffff !important;
    }

    .drop-menu .active span{
        color: #005b40 !important;
    }

    /* Sidebar Styles */
    

    .sidebar {
    max-width: 14rem !important;
    width: 100%;
    min-height: 100vh;
    background: #005b40; /* Set the background color to red */
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 1;
}


    .sidebar .logo {
        font-size: 1.6rem;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active {
        background-color: #ffffff;
        opacity: 1;
        color: #005b40;
    }

    .sidebar .nav-link:hover,
    .sidebar .nav-link.active span {
        background-color: #ffffff;
        opacity: 1;
        color: #005b40;
    }

    .sidebar .nav-link span {
        font-size: 1rem;
        color: #ffffff;
    }

    .sidebar .nav-link:hover span {
        font-size: 1rem;
        color: #005b40;
    }

    .sidebar .dropdown-toggle:after {
        display: none;
    }

    .sidebar .dropdown-menu {
        position: relative !important;
        padding: 0;
        margin: 0;
        width: 100%;
        overflow: hidden;
        transform: unset !important;
        top: unset !important;
        left: unset !important;
        min-width: unset !important;
        background-color: #0B6E4F;
        border-radius: 0 !important;
    }

    .sidebar .dropdown-item {
        padding: 0.8rem 0 0.8rem 1.5rem;
        margin: 0;
    }

    .sidebar .dropdown-item:hover,
    .sidebar .dropdown-item:active,
    .dropdown-item:focus {
        background-color: #005b40;
    }

    .sidebar .nav-link {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        font-size: 1rem;
        position: relative;
        opacity: 0.9;
        color: #ffffff;
    }

    .sidebar .fas.fa-caret-down.float-none.float-lg-right.fa-sm {
        position: absolute;
        top: 50%;
        right: 5%;
        transform: translate(-50%, -50%);
    }

    .sidebar.collapsed .nav-item:not(.logo-holder) {
        display: none !important;
    }

    @media (max-width: 991px) {
        .sidebar.mobile-hid .nav-item {
            display: none !important;
        }
    }

    .sidebar.collapsed #sidebarToggleHolder {
        position: absolute !important;
        color: #ffffff !important;
        left: 0;
        top: 0;
        padding: 10px;
        z-index: 999;
        margin-top: 3px;
    }

    @media (max-width: 991px) {
        .sidebar.mobile-hid #sidebarToggleHolder {
            position: absolute !important;
            color: #858791 !important;
            left: 0;
            top: 0;
            margin: 10px;
            z-index: 999;
        }
    }

    .sidebar.collapsed .logo #title {
        display: none;
    }

    @media (max-width: 991px) {
        .sidebar.mobile-hid .logo #title {
            display: none;
        }
    }

    .sidebar.collapsed #sidebarToggleHolder {
        float: none !important;
    }

    @media (max-width: 991px) {
        .sidebar.mobile-hid #sidebarToggleHolder {
            float: none !important;
        }
    }

    .sidebar.collapsed {
        width: 0 !important;
    }

    @media (max-width: 991px) {
        .sidebar.mobile-hid {
            width: 0 !important;
        }
    }

    .sidebar #sidebarToggleHolder {
        font-size: 20px !important;
        margin: 7px 5px;
    }

    .dropdown-item span {
        color: #ffffff;
    }

    .dropdown-item:hover span {
        color: #005b40;
    }

    .dropdown-item:hover {
        background-color: #ffffff !important;
        /*color: #ffffff;*/
    }

    .dropdown-menu .dropdown-item .active {
        background-color: #ffffff !important;
    }

    #title {
        color: #ffffff;
    }
    
    .flatpickr-calendar {
        box-shadow: none !important;
    }

    .tracking-container {
        text-align:center;
    }
    .tracking-item {
        position: relative;
        min-width: 150px;
        min-height: 50px;
        text-align: center;
        display: inline-block;
    }

    .tracking-item .item-box {
        height: 50px; 
        width:100%;
        padding: 2px 5px 2px 5px;
        border: 2px solid black;
        border-radius: 10px;
    }
    
    .tracking-container .tracking-item:not(:last-child) {
        margin-right: 17px;
    }
    .tracking-container .tracking-item:not(:last-child):before {
        position: absolute;
        content: '';
        right: -50%;
        margin-top: -8px;
        width: 50%;
        height: 50%;
        border-bottom: 10px solid black;
    }
    a {
        text-decoration: none;
    }
    .cars {
        position: absolute;
        margin-left: 120px;
    }

    .nav > .nav-item {
        width: 100%;
    }

    .nav-container {
        height: 100%;
        width: 100%;
        overflow-y: auto;
    }
    /* Scrollbar Styles */
/* For Chrome, Safari, and Opera browsers */
::-webkit-scrollbar {
    width: 8px;
    background-color: #f5f5f5;
}

::-webkit-scrollbar-thumb {
    background-color: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background-color: #555;
}

/* For Firefox browser */
::-moz-scrollbar {
    width: 8px;
    background-color: #f5f5f5;
}

::-moz-scrollbar-thumb {
    background-color: #888;
    border-radius: 4px;
}

::-moz-scrollbar-thumb:hover {
    background-color: #555;
}

    
</style>
@endsection
@section('content')
    <ul class="nav shadow d-flex sidebar mobile-hid">
        <li class="nav-item logo-holder">
            <div class="text-center text-white logo py-4 mx-4"><img class="img-fluid" src="/storage/assets/dnsc-logo.png" width="130px"><a class="float-end text-white" id="sidebarToggleHolder" href="#"><i class="fas fa-bars" id="sidebarToggle" style=""></i></a></div>
        </li>
    
        <div class="nav-container">
            @if (in_array(auth()->user()->role->role_name, ['Quality Assurance Director','Administrator']))
                @include('layout.admin')
            @elseif (auth()->user()->role->role_name == 'Document Control Custodian')
                @include('layout.dcc')
            @elseif (auth()->user()->role->role_name == 'Process Owner')
                @include('layout.po')
            @elseif (auth()->user()->role->role_name == 'Staff')
                @include('layout.staff')
            @elseif (auth()->user()->role->role_name == 'Human Resources')
                @include('layout.hr')
            @elseif (auth()->user()->role->role_name == 'Internal Lead Auditor')
                @include('layout.lead-auditor')
            @elseif (auth()->user()->role->role_name == 'Internal Auditor')
                @include('layout.auditor')
            @elseif (auth()->user()->role->role_name == 'College Management Team')
                @include('layout.cmt')
            @endif
        </div>
    </ul>
    <nav class="navbar navbar-light navbar-expand-md" style="background: rgb(9 60 47 / 90%);">
        <div class="container-fluid">
            <!-- Move the button container to the right -->
            <button data-bs-toggle="collapse" class="navbar-toggler ms-auto " data-bs-target="#navcol-1" style="color: #ffffff; border: none;">
                <span class="visually-hidden" >Toggle navigation</span> 
                <span class="navbar-toggler-icon"></span>
            </button>

            
        
            <div class="collapse navbar-collapse" id="navcol-1">
                <p class="navbar-text text-white ms-5" style="margin-bottom: 0;">OFFICE OF THE DIRECTOR FOR QUALITY ASSURANCE | <span class="fw-bold text-uppercase text-warning">{{ auth()->user()->role->role_name }}</span></p>
                <ul class="navbar-nav ms-auto">
                    <!-- Move the user profile and logout buttons to the right -->
                    <li class="nav-item me-2 ms-auto">
                        <div class="dropdown d-inline-block ms-2">

                            {{-- <button type="button" class="btn btn-sm text-warning position-relative m-3" id="page-header-notifications-dropdown" data-bs-toggle="dropdown">
                                <i class="fa fa-bell fa-2x"></i>
                                @if(!empty(auth()->user()->unreadNotifications) && count(auth()->user()->unreadNotifications) > 0)
                                <span class="position-absolute top-50 start-40 translate-middle badge rounded-pill" id="counter">
                                    <span id="output" class="badge rounded-pill bg-danger badge-lg">
                                        <span class="badge-count">{{ count(auth()->user()->unreadNotifications) }}</span>
                                    </span>
                                </span>
                                @endif
                            </button>
                            
                            <style>
                                .badge-count {
                                    font-size: .7rem; /* Adjust the font size to make it larger */
                                }
                            </style> --}}



                            <button type="button" class="btn btn-sm text-warning position-relative m-2" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style=" border:none">
                                <i class="fa fa-fw fa-bell fa-2x" style="font-size: 25px"></i>
                                @if(!empty(auth()->user()->unreadNotifications) && count(auth()->user()->unreadNotifications) > 0)
                                    <span class="fs-xs fw-semibold d-inline-block rounded-pill bg-danger text-white px-2 align-top position-absolute top-10 start-40 translate-middle" >{{ count(auth()->user()->unreadNotifications) }}</span>
                                @endif
                            </button>

                            <div class="dropdown-notification dropdown-menu dropdown-menu-lg dropdown-menu-end p-0 border-0 fs-sm" aria-labelledby="page-header-notifications-dropdown" style="">
                                <div class="p-2 bg-body-light border-bottom rounded-top">
                                    <h1 class="dropdown-header" style="font-size: 1.2em;">Notifications</h1>
                                  </div>
                                <ul class="nav-items no-bullets mb-0">
                                @if(!empty(auth()->user()->unreadNotifications) && count(auth()->user()->unreadNotifications) > 0)
                                @foreach(auth()->user()->unreadNotifications->take(5) as $notification)
                                <li class="d-flex align-items-center py-2">
                                    <a href="{{ $notification->data['link'] ?? route('notifications') }}?read={{ $notification->id}}" target="_blank" style="text-decoration: none;" class="d-flex align-items-center">
                                        @if (isset($notification->data['image']) && !empty($notification->data['image']))
                                            <div class="me-2">
                                                <img src="{{ Storage::url(auth()->user()->img) }}" alt="User Image" class="rounded-circle">
                                            </div>
                                        @else
                                            <div class="me-2">
                                                <img src="/media/logo.png" alt="Fallback Image" style="width: 24px; height: 24px; border-radius: 50%;">
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold">
                                                {{ $notification->data['message'] }}
                                            </div>
                                            <div class="fw-medium text-muted">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                                @else
                                <li>
                                    <div class="text-center">
                                        <small>No unread notifications found</small>
                                    </div>
                                    </li>
                                @endif
                                </ul>
                                <div class="p-2 border-top text-center">
                                    <a class="d-inline-block fw-medium" href="{{ route('notifications') }}" style="text-decoration: none;">
                                      <i class=" me-1 opacity-50"></i>&nbsp;&nbsp; View More
                                    </a>
                                  </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item me-2 ms-auto">
                        <a class="nav-link" href="{{ route('user.profile') }}">
                            <img src="{{ Storage::url(auth()->user()->img) }}" alt="User Image" class="rounded-circle"
                                style="width: 30px; height: 30px;">
                        </a>
                    </li>
                    <li class="nav-item me-2 ms-auto">
                        <a class="nav-link" href="{{ route('logout') }}">
                            <i class="fas fa-sign-out-alt text-warning" style="font-size: 27px; margin-top: 2px;"></i>

                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    
    @yield('page')
    {{-- <div class="d-flex h-100 sidebar-h">
        <div class="sidebar">
            @if (auth()->user()->role->role_name == 'Administrator')
                @include('layout.admin')
            @endif
        </div>
        <div class="overflow-auto w-100">
            <nav class="navbar navbar-expand-lg border-bottom border-dark ac" style="max-width:100%;height:4rem">
                <div class="container-fluid">
                    <span class="navbar-brand text-white">Office of the Director for Quality Assurance
                        ({{ auth()->user()->role->role_name }})</span>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                        data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                        aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                            <li class="nav-item dropdown dropstart">
                                <button class="nav-link btn mt-2 remove-design me-1" href="#" role="button" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    @if (auth()->user()->img)
                                        <img src="{{ asset('/storage/profiles/' . auth()->user()->img) }}"
                                            class="rounded-circle avatar" alt="your image">
                                    @else
                                        <span class="mdi mdi-account rounded-circle avatar"></span>
                                    @endif
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </li>
                            <li class="nav-item pt-1">
                                <a href="{{ route('logout') }}" class="nav-link"><span class="mdi mdi-power text-white" style="font-size: 1.5rem;"></span></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </nav>
            @yield('page')
        </div>
    </div> --}}
    @vite(['resources/js/sidebar.js'])
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            (function($) {
            let win = $(window);
            let w = win.width();

            let body = $('body');
            let btn = $('#sidebarToggle');
            let sidebar = $('.sidebar');

            // Collapse on load

            if (win.width() < 992) {
                sidebar.addClass('collapsed');
            }

            sidebar.removeClass('mobile-hid');

            // Events

            btn.click(toggleSidebar);

            win.resize(function() {

                if (w == win.width()) {
                    return;
                }

                w = win.width();

                if (w < 992 && !sidebar.hasClass('collapsed')) {
                    toggleSidebar();
                } else if (w > 992 && sidebar.hasClass('collapsed')) {
                    toggleSidebar();
                }
            });

            function toggleSidebar() {

                if (win.width() < 992 || !sidebar.hasClass('collapsed')) {
                    body.animate({
                        'padding-left': '0'
                    }, 100);
                } else if (win.width() > 992 && sidebar.hasClass('collapsed')) {
                    body.animate({
                        'padding-left': '14rem'
                    }, 100);
                }

                if (!sidebar.hasClass('collapsed')) {
                    sidebar.fadeOut(100, function() {
                        btn.hide();
                        sidebar.addClass('collapsed');
                        btn.fadeIn(100);
                    });
                } else {
                    sidebar.removeClass('collapsed');
                    sidebar.fadeIn(100);
                }

            }
            })(jQuery) 
        });
    </script>
@endsection
