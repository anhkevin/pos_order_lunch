@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Chi tiết Order</div>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                    <!-- <order-progress status="{{ $order->status->name}}" initial=" {{ $order->status->percent }}" order_id="{{ $order->id }}"></order-progress> -->

                    <order-alert user_id="{{ auth()->user()->id }}"></order-alert>



                    <br>

                    <div class="order-details">
                        <strong>Order ID:</strong> {{ $order->id }} <br>
                        <strong>User:</strong> {{ $order->address }} <br><br>
                        <strong>Cơm:</strong> {{ $order->size }} <br>
                        <strong>Món Thêm:</strong> {{ $order->toppings }} <br>

                        @if ($order->instructions != '')
                            <strong>Ghi chú:</strong> {{ $order->instructions }}
                        @endif

                        <p style="padding-top: 10px;font-weight: bold;color: red;font-size: 120%;">
                            <strong>Tổng tiền:</strong> {{ number_format($order->amount, 0, ".", ",") . "đ" }} 
                            <br>
                            @if ($order->status_column_name != 'paid')
                            <strong style="color: #ba7171;">Trạng thái:</strong> <span style="color: #ba7171;">{{ $order->status_name }} </span>
                            @else
                            <strong style="color: #1c81f5;">Trạng thái:</strong> <span style="color: #1c81f5;">{{ $order->status_name }} </span>
                            @endif
                        </p>

                    </div>
                    <!-- @if ($order->status_column_name != 'paid' && $order->status_column_name != 'cancel')
                        <a class="btn btn-primary" href="#" disabled="disabled">QR Momo <br>(common soon)</a>
                    @endif -->

                </div> <!-- end panel-body -->
            </div> <!-- end panel -->
        </div>
    </div>
</div>
@endsection
