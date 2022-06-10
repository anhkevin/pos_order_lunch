@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Danh sách Order <span style="color:red">chưa thanh toán</span></div>

                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                @if ($orders->count() == 0)
                    <p>No orders yet.</p>

                @else

                    <order-alert user_id="{{ auth()->user()->id }}"></order-alert>
                    @php($total_amount=0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>User</th>
                                    <th>Cơm</th>
                                    <th>Món thêm</th>
                                    <th>Tổng tiền</th>
                                    <th>Status</th>
                                    <!-- <th>Thanh toán</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @if ($order->status->column_name == 'cancel')
                                    <tr style="display:none">
                                    @else
                                    @php($total_amount+=$order->amount-$order->discount)
                                    <tr>
                                    @endif
                                        <td>{{ date_format($order->created_at ,"Y/m/d") }}</td>
                                        <td>{{ $order->customer->name }}</td>
                                        <td>{{ $order->size }}</td>
                                        <td>{{ Str::words($order->toppings, 3) }}</td>
                                        <td>
                                            @if ($order->discount > 0)
                                                <span style="text-decoration-line: line-through;">{{ number_format($order->amount, 0, ".", ",") . "đ" }}</span><br>
                                                <span style="color: #bc0c0c;font-weight: bold;">{{ number_format($order->amount-$order->discount, 0, ".", ",") . "đ" }}</span><br>
                                            @else
                                                {{ number_format($order->amount, 0, ".", ",") . "đ" }}
                                            @endif
                                        </td>
                                        @if ($order->status->column_name != 'paid')
                                            <td style="color: rgb(150 62 62);font-weight: bold;">
                                        @else
                                            <td>
                                        @endif
                                        {{ $order->status->name }}</td>
                                        <!-- <td>
                                            @if ($order->user_id == auth()->user()->id)
                                                <a class="btn btn-primary" href="#" disabled="disabled">QR Momo <br>(common soon)</a>
                                            @endif
                                        </td> -->
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="color:red;font-weight:bold">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Tổng tiền</td>
                                    <td>{{ number_format($total_amount, 0, ".", ",") . "đ" }}</td>
                                    <td></td>
                                    <!-- <td></td> -->
                                </tr>
                            </tfood>

                        </table>
                    </div> <!-- end table-responsive -->

                @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection