<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Đặt cơm</title>

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: Arial,Helvetica,sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links {
                margin-bottom: 30px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
                border: 2px solid #ccc;
                padding-top: 15px;
                padding-bottom: 15px;
                background-color: #b8cffc;
            }

            .m-b-md {
                margin-bottom: 30px;
            }

            .hero-image {
                padding-top: 15px;
                margin: auto;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">

            <div class="content">
                <div class="title m-b-md">
                    POS: Đặt Cơm
                </div>

                @if (Route::has('login'))
                    <div class="links">
                        @auth
                            <a href="{{ url('/orders') }}">Home</a>
                        @else
                            <a href="{{ route('login') }}">Login</a>
                            <a href="{{ route('register') }}">Register</a>
                        @endauth
                        <a target="_blank" href="https://docs.google.com/spreadsheets/d/1yocaM5PcOeIsuqR-ivARvHBfzI3XhN0Sog0KILt6F8U">Góp Ý</a>
                    </div>
                @endif

                <div class="hero-image">
                    <img src="{{ url('img/icon_order_lunch.jpeg') }}" width="400px">
                </div>

            </div>
        </div>
    </body>
</html>
