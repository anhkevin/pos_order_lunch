@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <div class="card-header">
                <h2 class="text-black font-w600 mb-0">My Wallet ({{$user->name}})</h2>
            </div>
            <div class="card-body">

                <div class="">
                    <div class="d-flex flex-wrap border-0 pb-0 align-items-end justify-content-between">
                        <div class="d-flex align-items-center mb-3 mr-3">
                            <svg class="mr-3" width="68" height="68" viewBox="0 0 68 68" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M59.4999 31.688V19.8333C59.4999 19.0818 59.2014 18.3612 58.6701 17.8298C58.1387 17.2985 57.418 17 56.6666 17H11.3333C10.5818 17 9.86114 16.7014 9.32978 16.1701C8.79843 15.6387 8.49992 14.9181 8.49992 14.1666C8.49992 13.4152 8.79843 12.6945 9.32978 12.1632C9.86114 11.6318 10.5818 11.3333 11.3333 11.3333H56.6666C57.418 11.3333 58.1387 11.0348 58.6701 10.5034C59.2014 9.97208 59.4999 9.25141 59.4999 8.49996C59.4999 7.74851 59.2014 7.02784 58.6701 6.49649C58.1387 5.96514 57.418 5.66663 56.6666 5.66663H11.3333C9.07891 5.66663 6.9169 6.56216 5.32284 8.15622C3.72878 9.75028 2.83325 11.9123 2.83325 14.1666V53.8333C2.83325 56.0876 3.72878 58.2496 5.32284 59.8437C6.9169 61.4378 9.07891 62.3333 11.3333 62.3333H56.6666C57.418 62.3333 58.1387 62.0348 58.6701 61.5034C59.2014 60.9721 59.4999 60.2514 59.4999 59.5V47.6453C61.1561 47.0683 62.5917 45.9902 63.6076 44.5605C64.6235 43.1308 65.1693 41.4205 65.1693 39.6666C65.1693 37.9128 64.6235 36.2024 63.6076 34.7727C62.5917 33.3431 61.1561 32.265 59.4999 31.688ZM53.8333 56.6666H11.3333C10.5818 56.6666 9.86114 56.3681 9.32978 55.8368C8.79843 55.3054 8.49992 54.5847 8.49992 53.8333V22.1453C9.40731 22.4809 10.3658 22.6572 11.3333 22.6666H53.8333V31.1666H45.3333C43.0789 31.1666 40.9169 32.0622 39.3228 33.6562C37.7288 35.2503 36.8333 37.4123 36.8333 39.6666C36.8333 41.921 37.7288 44.083 39.3228 45.677C40.9169 47.2711 43.0789 48.1666 45.3333 48.1666H53.8333V56.6666ZM56.6666 42.5H45.3333C44.5818 42.5 43.8611 42.2015 43.3298 41.6701C42.7984 41.1387 42.4999 40.4181 42.4999 39.6666C42.4999 38.9152 42.7984 38.1945 43.3298 37.6632C43.8611 37.1318 44.5818 36.8333 45.3333 36.8333H56.6666C57.418 36.8333 58.1387 37.1318 58.6701 37.6632C59.2014 38.1945 59.4999 38.9152 59.4999 39.6666C59.4999 40.4181 59.2014 41.1387 58.6701 41.6701C58.1387 42.2015 57.418 42.5 56.6666 42.5Z" fill="#1EAAE7"></path>
                            </svg>
                            <div class="mr-auto ">
                                <h5 class="fs-20 text-black font-w600">Tổng tiền còn lại</h5>
                                <div class="d-flex align-items-center">
                                    <span class="text-num text-black font-w600 mr-3"><strong>{{ number_format($user->total_money, 0, ".", ",") . "đ" }}</strong></span>
                                    <a href="https://me.momo.vn/anhkevin" target="_blank" class="btn btn-success btn-xs">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nạp Tiền
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="mr-3 mb-3">
                            <p class="fs-14 mb-1">Tổng tiền đã nạp</p>
                            <span class="text-black"><strong>{{ number_format($user->total_deposit, 0, ".", ",") . "đ" }}</strong></span>
                        </div>
                        <div class="mr-3 mb-3">
                            <p class="fs-14 mb-1">Tổng tiền sử dụng Order</p>
                            <span class="text-black"><strong>{{ number_format($user->total_paid, 0, ".", ",") . "đ" }}</strong></span>
                        </div>
                    </div>
                </div>

                <div class="">
                    <transaction-history-component user_id="{{ $user->id }}"></transaction-history-component>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
