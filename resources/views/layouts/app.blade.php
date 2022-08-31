<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#6777ef"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="green">
    <meta name="apple-mobile-web-app-title" content="POS Food">
    <link rel="apple-touch-icon" href="{{URL::asset('icon.png')}}">
    <link rel="manifest" href="{{URL::asset('manifest.json')}}">
    <link rel="icon" type="image/x-icon" href="{{URL::asset('images/favicon.ico')}}">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">

        @if(url()->current() != route('login') && url()->current() != route('register'))
        <div id="main-wrapper" class="show">
            <div class="nav-header">
                <a href="/" class="brand-logo">
                    <img class="logo-abbr" src="{{URL::asset('images/logo.png')}}" alt="">
                    <img class="logo-compact" src="{{URL::asset('images/logo-text.png')}}" alt="">
                    <img class="brand-title" src="{{URL::asset('images/logo-text.png')}}" alt="">
                </a>

                <div class="nav-control">
                    <div class="hamburger">
                        <span class="line"></span><span class="line"></span><span class="line"></span>
                    </div>
                </div>
            </div>

            <!--**********************************
        Header start
***********************************-->
        
            <div class="header">
                <div class="header-content">
                    <nav class="navbar navbar-expand">
                        <div class="collapse navbar-collapse justify-content-between">
                            <div class="header-left">
                                <div class="dashboard_bar">
                                    <!-- <div class="input-group search-area d-lg-inline-flex d-none">
                                        <div class="input-group-append">
                                            <button class="input-group-text"><i class="flaticon-381-search-2"></i></button>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Search here...">
                                    </div> -->
                                </div>
                            </div>
                            <ul class="navbar-nav header-right">
                                <li class="nav-item"><a href="javascript:void(0)" id="installApp" style="display:none" class="btn btn-outline-primary"><i class="las la-download scale5 mr-3"></i>Web APP</a></li>
                                @auth
                                <li class="nav-item">
                                    <div class="d-flex weather-detail">
                                        <span><i class="las la-wallet"></i>{{ number_format(Auth::user()->total_money, 0, ".", ",") . "đ" }}</span>
                                    </div>
                                </li>

                                
                                <li class="nav-item dropdown notification_dropdown">
                                    <a class="nav-link  ai-icon" href="javascript:void(0)" role="button" data-toggle="dropdown">
                                        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.6001 4.3008V1.4C12.6001 0.627199 13.2273 0 14.0001 0C14.7715 0 15.4001 0.627199 15.4001 1.4V4.3008C17.4805 4.6004 19.4251 5.56639 20.9287 7.06999C22.7669 8.90819 23.8001 11.4016 23.8001 14V19.2696L24.9327 21.5348C25.4745 22.6198 25.4171 23.9078 24.7787 24.9396C24.1417 25.9714 23.0147 26.6 21.8023 26.6H15.4001C15.4001 27.3728 14.7715 28 14.0001 28C13.2273 28 12.6001 27.3728 12.6001 26.6H6.19791C4.98411 26.6 3.85714 25.9714 3.22014 24.9396C2.58174 23.9078 2.52433 22.6198 3.06753 21.5348L4.20011 19.2696V14C4.20011 11.4016 5.23194 8.90819 7.07013 7.06999C8.57513 5.56639 10.5183 4.6004 12.6001 4.3008ZM14.0001 6.99998C12.1423 6.99998 10.3629 7.73779 9.04973 9.05099C7.73653 10.3628 7.00011 12.1436 7.00011 14V19.6C7.00011 19.817 6.94833 20.0312 6.85173 20.2258C6.85173 20.2258 6.22871 21.4718 5.57072 22.7864C5.46292 23.0034 5.47412 23.2624 5.60152 23.4682C5.72892 23.674 5.95431 23.8 6.19791 23.8H21.8023C22.0445 23.8 22.2699 23.674 22.3973 23.4682C22.5247 23.2624 22.5359 23.0034 22.4281 22.7864C21.7701 21.4718 21.1471 20.2258 21.1471 20.2258C21.0505 20.0312 21.0001 19.817 21.0001 19.6V14C21.0001 12.1436 20.2623 10.3628 18.9491 9.05099C17.6359 7.73779 15.8565 6.99998 14.0001 6.99998Z" fill="#3E4954"></path>
                                        </svg>
                                        <span class="badge light text-white bg-primary rounded-circle">12</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <div id="DZ_W_Notification1" class="widget-media dz-scroll p-3 height380 ps">
                                            <ul class="timeline">
                                                <li>
                                                    <div class="timeline-panel">
                                                        <div class="media mr-2">
                                                            <img alt="image" width="50" src="{{URL::asset('images/avatar/1.jpg')}}">
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Dr sultads Send you Photo</h6>
                                                            <small class="d-block">29 July 2020 - 02:26 PM</small>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="timeline-panel">
                                                        <div class="media mr-2 media-info">
                                                            KG
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Resport created successfully</h6>
                                                            <small class="d-block">29 July 2020 - 02:26 PM</small>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="timeline-panel">
                                                        <div class="media mr-2 media-success">
                                                            <i class="fa fa-home"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Reminder : Treatment Time!</h6>
                                                            <small class="d-block">29 July 2020 - 02:26 PM</small>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="timeline-panel">
                                                        <div class="media mr-2">
                                                            <img alt="image" width="50" src="{{URL::asset('images/avatar/1.jpg')}}">
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Dr sultads Send you Photo</h6>
                                                            <small class="d-block">29 July 2020 - 02:26 PM</small>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="timeline-panel">
                                                        <div class="media mr-2 media-danger">
                                                            KG
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Resport created successfully</h6>
                                                            <small class="d-block">29 July 2020 - 02:26 PM</small>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="timeline-panel">
                                                        <div class="media mr-2 media-primary">
                                                            <i class="fa fa-home"></i>
                                                        </div>
                                                        <div class="media-body">
                                                            <h6 class="mb-1">Reminder : Treatment Time!</h6>
                                                            <small class="d-block">29 July 2020 - 02:26 PM</small>
                                                        </div>
                                                    </div>
                                                </li>
                                            </ul>
                                        <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div>
                                        <a class="all-notification" href="javascript:void(0)">See all notifications <i class="ti-arrow-right"></i></a>
                                    </div>
                                </li>
                                <li class="nav-item dropdown header-profile">
                                    <a class="nav-link" href="javascript:void(0)" role="button" data-toggle="dropdown">
                                        <div class="header-info">
                                            <span class="text-black">Hello,<strong> {{ Auth::user()->name }}</strong></span>
                                            @if (Auth::user()->is_admin == 1)<p class="fs-12 mb-0">Super Admin</p>@else<p class="fs-12 mb-0">Member</p>@endif
                                        </div>
                                        <avatar-component size="md"  userid="{{Auth::user()->id}}" :isauth="true"></avatar-component>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-right">
                                        <a href="{{ route('logout') }}"
                                            class="dropdown-item ai-icon"
                                            onclick="event.preventDefault();
                                                        document.getElementById('logout-form').submit();">
                                            <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                            <span class="ml-2">Logout </span>
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>

                                    </div>
                                </li>
                                @endauth
                                
                                @guest
                                <li class="nav-item">
                                    <div class="d-flex ">
                                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Login</a>
                                    </div>
                                </li>

                                <li class="nav-item">
                                    <div class="d-flex ">
                                    <a href="{{ route('register') }}" class="btn btn-outline-primary">Register</a>
                                    </div>
                                </li>
                                @endguest
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>
            @endif

            
            
    <!--**********************************
        Header end ti-comment-alt
    ***********************************-->	

    <!--**********************************
    Sidebar start
    ***********************************-->
            
            <div class="deznav">
                <div class="deznav-scroll ps between_element">
                    <ul class="metismenu" id="menu">
                        @if(url()->current() != route('login') && url()->current() != route('register'))              
                        <li class="{{ url()->current() == route('dashboard') ? 'mm-active' : ''}}">
                            <a class="ai-icon" href="{{ route('dashboard') }}" aria-expanded="false">
                                <i class="flaticon-381-networking"></i>
                                <span class="nav-text">Dashboard</span>
                            </a>
                        </li>
                        @endif

                        @auth
                        <!-- <li>
                            <a class="ai-icon" href="javascript:void()" aria-expanded="false">
                                <img src="{{URL::asset('/img/file.png')}}" alt="" class="w-full icon-sidebar">
                                <span class="nav-text">Danh sách quán ăn</span>
                            </a>
                        </li> -->

                        <li class="{{ url()->current() == route('user.orders') ? 'mm-active' : ''}}">
                            <a class="ai-icon" href="{{ route('user.orders') }}" aria-expanded="false">
                                <i class="flaticon-381-network"></i>
                                <span class="nav-text">My Orders</span>
                            </a>
                        </li>

                        <li class="{{ url()->current() == route('user.orders.create') ? 'mm-active' : ''}}">
                            <a class="ai-icon" href="{{ route('user.orders.create') }}" aria-expanded="false">
                                <i class="flaticon-381-notepad"></i>
                                <span class="nav-text">Đặt món</span>
                            </a>
                        </li>

                        <li>
                            <a class="has-arrow ai-icon" href="javascript:void()" aria-expanded="false">
                                <i class="flaticon-381-television"></i>
                                <span class="nav-text">Thống kê</span>
                            </a>
                            <ul aria-expanded="false" class="mm-collapse">
                                <li class="{{ url()->current() == route('user.orders.today') ? 'mm-active' : ''}}"><a href="{{ route('user.orders.today') }}">Orders hôm nay</a></li>
                                <li class="{{ url()->current() == route('user.orders.product') ? 'mm-active' : ''}}"><a href="{{ route('user.orders.product') }}">Món đặt hôm nay</a></li>
                                <li class="{{ url()->current() == route('user.orders.debt') ? 'mm-active' : ''}}"><a href="{{ route('user.orders.debt') }}">Công nợ</a></li>
                                <li class="{{ url()->current() == route('wallet.show') ? 'mm-active' : ''}}"><a href="{{ route('wallet.show') }}">Danh sách Wallet</a></li>                
                            </ul>
                        </li>

                        <li class="{{ url()->current() == route('wallet.index') ? 'mm-active' : ''}}">
                            <a class="ai-icon" href="{{ route('wallet.index') }}" aria-expanded="false">
                            <i class="flaticon-381-controls-2"></i>
                            <span class="nav-text">My wallet</span>
                            </a>
                        </li>

                        <li>
                            <a class="ai-icon" target="_blank" href="https://docs.google.com/spreadsheets/d/1yocaM5PcOeIsuqR-ivARvHBfzI3XhN0Sog0KILt6F8U" aria-expanded="false">
                                <i class="flaticon-381-note"></i>
                                <span class="nav-text">Góp ý</span>
                            </a>
                        </li>
                        
                        @if (Auth::user()->is_admin == 1)
                        <li class="{{ url()->current() == route('admin.orders') ? 'mm-active' : ''}}">
                            <a class="ai-icon" href="{{ route('admin.orders') }}" aria-expanded="false">
                                <i class="flaticon-381-user"></i>
                                <span class="nav-text">Admin</span>
                            </a>
                        </li>
                        @endif
                        @endauth
                        
                    </ul>

                    @if(url()->current() != route('login') && url()->current() != route('register'))
                    <div class="copyright">
                        <p>© 2022 All Rights Reserved</p>
                        <p>Made with <span class="heart"></span> by </p><p>TONIDEV & TIANDEV</p>
                    </div>
                    @endif
                    
                </div>
            </div>


            <div class="content-body" style="min-height: 1140px;">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
        
    </div>

    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ mix('js/vendor.js') }}"></script>
    <script src="{{ asset('/sw.js') }}"></script>
    <script>
        if (!navigator.serviceWorker.controller) {
            navigator.serviceWorker.register("/sw.js").then(function (reg) {
                console.log("Service worker has been registered for scope: " + reg.scope);
            });
        }
        // let deferredPrompt;
        // const installApp = document.getElementById('installApp');
        // window.addEventListener('beforeinstallprompt', (e) => {
        //     installApp.style.display = 'block';
        //     deferredPrompt = e;
        // });

        // installApp.addEventListener('click', async () => {
        //     if (deferredPrompt !== null) {
        //         deferredPrompt.prompt();
        //         const { outcome } = await deferredPrompt.userChoice;
        //         if (outcome === 'accepted') {
        //             deferredPrompt = null;
        //             installApp.style.display = 'none';
        //         }
        //     }
        // });
    </script>

    @yield('extra-js')
</body>
</html>
