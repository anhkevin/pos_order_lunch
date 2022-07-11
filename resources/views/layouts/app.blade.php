<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a class="navbar-brand" href="{{ url('/') }}" style="background: #909090;background: #fff;font-weight: bold;">
                        {{ config('app.name', 'Laravel') }}
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">
                        @auth
                            <li><a href="{{ route('user.orders') }}">My Orders</a></li>
                            <li><a href="{{ route('user.orders.create') }}">Add Order</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    Thống Kê <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="{{ route('user.orders.today') }}">Orders hôm nay</a></li>
                                    <li><a href="{{ route('user.orders.product') }}">Món đặt hôm nay</a></li>
                                    <li><a href="{{ route('user.orders.debt') }}">Công nợ</a></li>
                                    <li><a href="{{ route('wallet.show') }}">Danh sách Wallet</a></li>
                                </ul>
                            </li>

                            {{-- Admin Routes --}}
                            @if (Auth::user()->is_admin == 1)
                                <li><a href="{{ route('admin.orders') }}">ADMIN</a></li>
                            @endif
                            <li><a href="#" style="color: #ff0000;font-style: italic;padding-left: 100px;">Thanh Toán Momo: <strong>0949913393</strong> </a></li>
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            <li><a href="{{ route('register') }}">Register</a></li>
                        @else

                            <li><a href="{{ route('wallet.index') }}"><strong>My Wallet</strong><small style="display: block;line-height: 1;text-align: center;color: red;">{{ "( ".number_format(Auth::user()->total_money, 0, ".", ",") . "đ )" }}</small></a></li>
                            @include('partials.notifications-dropdown')

                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <script src="{{ mix('js/app.js') }}"></script>

    @yield('extra-js')
</body>
</html>
