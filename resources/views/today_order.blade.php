@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Danh sách Order ngày <span style="color:red">{{ date("Y/m/d") }}</span></div>
                @if ($orders->count() > 0)
                <br>
                <form class="form-horizontal" id="update-order-status-today" method="post" action="{{ route('admin.orders.update_status_today') }}">
                    {{ csrf_field() }}
                    {!! method_field('patch') !!}
                    <strong style="margin-right:30px;">Tình trạng Order: </strong>
                    @if (empty($order_status) || (isset($order_status->column_name) && $order_status->column_name == 'order'))
                        <button type="submit" name="status_order" value="1" class="btn btn-danger btn-status-order">Add Order</button>
                    @else
                        <button type="submit" name="status_order" value="1" class="btn btn-info btn-status-order">Add Order</button>
                    @endif
                    @if (isset($order_status->column_name) && $order_status->column_name == 'booked')
                        <button type="submit" name="status_booked" value="1" class="btn btn-danger btn-status-order">Đã đặt</button>
                    @else
                        <button type="submit" name="status_booked" value="1" class="btn btn-info btn-status-order">Đã đặt</button>
                    @endif
                    @if (isset($order_status->column_name) && $order_status->column_name == 'unpaid')
                        <button type="submit" name="status_unpaid" value="1" class="btn btn-danger btn-status-order">Thanh toán</button>
                    @else
                        <button type="submit" name="status_unpaid" value="1" class="btn btn-info btn-status-order">Thanh toán</button>
                    @endif
                </form>
                <br>
                @endif
                <div class="panel-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                @if ($orders->count() == 0)
                    <p>No orders yet.</p>
                    <a class="btn btn-success" href="{{ route('user.orders.create') }}">Add Order</a>

                @else

                    @php($total_amount=0)
                    @php($total_amount_discount=0)
                    <form class="form-horizontal" id="update-voucher" method="post" action="{{ route('admin.orders.update_voucher') }}">
                        {{ csrf_field() }}
                        {!! method_field('patch') !!}
                    <p style="padding-left: 100px;"><strong style="width: 100px;display: inline-block;">Phí ship:</strong> {{ number_format($shop_info->ship, 0, ".", ",") . "đ" }}</p>
                    <p style="padding-left: 100px;"><strong style="width: 100px;display: inline-block;">Voucher:</strong> {{ number_format($shop_info->voucher, 0, ".", ",") . "đ" }}  <button type="submit" id="update-discount-today" class="btn btn-success" style="padding: 2px 10px;margin-left:20px;">Áp dụng Voucher + phí Ship</button></p>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>User</th>
                                    <th>Cơm</th>
                                    <th>Món thêm</th>
                                    <th>Tổng tiền</th>
                                    <th>Ghi chú</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($count_order=0)
                                @foreach ($orders as $order)
                                    @if ($order->status->column_name == 'cancel')
                                    <tr style="display:none">
                                    @elseif ($order->status->column_name == 'unpaid')
                                    <tr style="background:#d1a3a3;">
                                    @else
                                    <tr>
                                    @endif

                                    @if ($order->status->column_name != 'cancel')
                                        @php($total_amount+=$order->amount)
                                        @php($total_amount_discount+=$order->amount-$order->discount)
                                        @php($count_order++)
                                    @endif
                                        <td>{{ $count_order }}<input type="hidden" name="order_id[]" value="{{ $order->id }}"></td>
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
                                        <td>{{ Str::words($order->instructions, 4) }}</td>
                                        @if ($order->status->column_name != 'paid')
                                            <td style="color: rgb(150 62 62);font-weight: bold;">
                                        @else
                                            <td>
                                        @endif
                                        {{ $order->status->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="color:red;font-weight:bold">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>Tổng tiền</td>
                                    <td>{{ number_format($total_amount_discount, 0, ".", ",") . "đ" }}</td>
                                    <td><input type="hidden" name="discount" value="{{ ($shop_info->voucher-$shop_info->ship)/$total_amount }}"></td>
                                    <td></td>
                                </tr>
                            </tfood>

                        </table>
                        
                    </div> <!-- end table-responsive -->
                    </form>

                @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
