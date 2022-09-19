@extends('layouts.app')

@section('content')
<div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">My Orders</h4>
                </div>

                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                @if ($orders->count() == 0)
                    <p>No orders yet.</p>
                    <a class="btn btn-success" href="{{ route('user.orders.create') }}">Add Order</a>

                @else

                    <order-alert user_id="{{ auth()->user()->id }}"></order-alert>

                    <div class="table-responsive">
                        <table class="table table-responsive-md">
                            <thead>
                                <tr>
                                    <th class="width80">Date</th>
                                    <th>Món đã đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Ghi chú</th>
                                    <th>Status</th>
                                    <th>Option</th>
                                    <!-- <th>Thanh toán</th> -->
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders as $order)
                                    @if ($order->status->column_name == 'cancel')
                                    <tr style="background: #c1c1c1;">
                                    @elseif ($order->status->column_name == 'unpaid')
                                    <tr class="blink">
                                    @else
                                    <tr>
                                    @endif
                                        <td><a href="{{ route('user.orders.show', $order) }}">{{ date_format($order->created_at ,"Y/m/d") }}</a></td>
                                        <td>{!! nl2br2(e($order->size)) !!}</td>
                                        <td>
                                            @if ($order->discount > 0)
                                                <span style="text-decoration-line: line-through;">{{ number_format($order->amount, 0, ".", ",") . "đ" }}</span><br>
                                                <span style="color: #bc0c0c;font-weight: bold;">{{ number_format($order->amount-$order->discount, 0, ".", ",") . "đ" }}</span><br>
                                            @else
                                                {{ number_format($order->amount, 0, ".", ",") . "đ" }}
                                            @endif
                                        </td>
                                        <td>{{ Str::words($order->instructions, 50) }}</td>
                                        <td id="text_status_{{ $order->id }}">
                                            @if ($order->order_types->pay_type == 2)
                                                {!! html_poll_status($order->status->column_name, $order->status->name, isset($order->history_payments) ? 1 : 0) !!}
                                            @else
                                                {!! html_order_status($order->status->column_name, $order->status->name, (isset($order->order_types, $order->history_payments) && $order->order_types->pay_type == 1) ? 1 : 0) !!}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($order->status->column_name == 'order')
                                                <a class="btn btn-success" style="display:none" href="{{ route('user.orders.edit', $order) }}">Edit</a>
                                                <form class="form-horizontal" method="post" action="{{ route('user.orders.cancel', $order) }}" style="display: inline-block;">
                                                    {{ csrf_field() }}
                                                    {!! method_field('patch') !!}
                                                    <button type="submit" name="btn_cancel" class="btn btn-danger">Cancel</button>
                                                </form>
                                            @endif
                                            @if (isset($order->order_types) && auth()->user()->id != $order->order_types->assign_user_id && $order->order_types->pay_type == 1 && in_array($order->status->column_name, array('booked', 'unpaid')))
                                                <pay-order-type order_id="{{ $order->id }}"></pay-order-type>
                                            @endif
                                        </td>
                                        <!-- <td>
                                            @if ($order->status->column_name != 'paid' && $order->status->column_name != 'cancel')
                                                <a class="btn btn-primary" href="{{ route('user.momo.create', $order) }}">QR Momo</a>
                                            @endif
                                        </td> -->
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>
                        
                    </div> <!-- end table-responsive -->

                @endif

                </div>
            </div>
        </div>
    </div>
@endsection
