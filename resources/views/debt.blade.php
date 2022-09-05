@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Danh sách Order <span style="color:red">chưa thanh toán</span></div>

            <div class="card-body">
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
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="width:80px">Date</th>
                                <th style="width:100px">User</th>
                                <th>Cơm</th>
                                <th style="width:80px">Tổng tiền</th>
                                <th style="width:120px">Status</th>
                                @if (auth()->user()->is_admin)
                                    <th>Option</th>
                                @endif
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
                                    <td>
                                        <div class="d-flex align-items-center width150" style="gap: 5px;">
                                            <avatar-component size="sm" userid="{{ $order->customer->id }}" class="mr10"></avatar-component>
                                            {{ $order->customer->name }}
                                        </div>
                                    </td>
                                    <td>{!! nl2br2(e($order->size)) !!}</td>
                                    <td>
                                        @if ($order->discount > 0)
                                            <span style="text-decoration-line: line-through;">{{ number_format($order->amount, 0, ".", ",") . "đ" }}</span><br>
                                            <span style="color: #bc0c0c;font-weight: bold;">{{ number_format($order->amount-$order->discount, 0, ".", ",") . "đ" }}</span><br>
                                        @else
                                            {{ number_format($order->amount, 0, ".", ",") . "đ" }}
                                        @endif
                                    </td>
                                    <td>{!! html_order_status($order->status->column_name, $order->status->name) !!}</td>
                                    @if (auth()->user()->is_admin || auth()->user()->id == $order->assign_user_id)
                                    <td>
                                        <form class="form-horizontal" id="form-order-{{$order->id}}" method="post" action="{{ route('admin.orders.update', $order) }}">
                                            {{ csrf_field() }}
                                            {!! method_field('patch') !!}
                                            <input type="hidden" name="status_id" value="4">
                                            <button type="submit" id="update-order-{{$order->id}}" class="btn btn-danger">Thanh Toán</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="color:red;font-weight:bold">
                                <td></td>
                                <td></td>
                                <td>Tổng tiền</td>
                                <td>{{ number_format($total_amount, 0, ".", ",") . "đ" }}</td>
                                <td></td>
                                @if (auth()->user()->is_admin)
                                <td></td>
                                @endif
                            </tr>
                        </tfood>

                    </table>
                </div> <!-- end table-responsive -->

            @endif

            </div>
        </div>
    </div>
</div>
@endsection
