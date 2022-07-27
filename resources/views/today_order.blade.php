@extends('layouts.app')

@section('content')
<div class="">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                <h2 class="text-black font-w600 mb-0">Danh sách món Order @if ($title)<small>({{ $title }})</small>@endif</h2></div>
                
                <div class="card-body bootstrap-badge" style="padding-top: 0;">
                    @if ($list_order_type->count() > 1)
                        @foreach ($list_order_type as $order_type)
                            @if ((empty($_GET['order_type']) && $order_type->is_default == 1) || (!empty($_GET['order_type']) && $_GET['order_type'] == base64_encode($order_type->id)))
                                <a href="{{ route('user.orders.today') }}?order_type={{ base64_encode($order_type->id) }}" class="badge badge-primary">{{ $order_type->order_name }}</a>
                            @else
                                <a href="{{ route('user.orders.today') }}?order_type={{ base64_encode($order_type->id) }}" class="badge badge-dark">{{ $order_type->order_name }}</a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="card-body">

                    @if ($orders->count() > 0)
                    <form class="form-horizontal" id="update-order-status-today" method="post" action="{{ route('admin.orders.update_status_today') }}">
                        {{ csrf_field() }}
                        {!! method_field('patch') !!}
                        <strong style="margin-right:30px;">Tình trạng Order: </strong>
                        @if (empty($order_status) || (isset($order_status->column_name) && $order_status->column_name == 'order'))
                            <button type="submit" name="status_order" value="1" class="btn btn-danger btn-status-order btn-xs">Add Order</button>
                        @else
                            <button type="submit" name="status_order" value="1" class="btn btn-info btn-status-order btn-xs">Add Order</button>
                        @endif
                        @if (isset($order_status->column_name) && $order_status->column_name == 'booked')
                            <button type="submit" name="status_booked" value="1" class="btn btn-danger btn-status-order btn-xs">Đã đặt</button>
                        @else
                            <button type="submit" name="status_booked" value="1" class="btn btn-info btn-status-order btn-xs">Đã đặt</button>
                        @endif
                        @if (isset($order_status->column_name) && $order_status->column_name == 'unpaid')
                            <button type="submit" name="status_unpaid" value="1" class="btn btn-danger btn-status-order btn-xs">Thanh toán</button>
                        @else
                            <button type="submit" name="status_unpaid" value="1" class="btn btn-info btn-status-order btn-xs">Thanh toán</button>
                        @endif

                        @if (isset($_GET['order_type']))
                        <input type="hidden" name="order_type" value="{{ $_GET['order_type'] }}">
                        @endif
                    </form>
                    <br>
                    @endif

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
                        @if (isset($_GET['order_type']))
                        <input type="hidden" name="order_type" value="{{ $_GET['order_type'] }}">
                        @endif
                    <p><strong style="width: 100px;display: inline-block;">Phí ship:</strong> {{ number_format($shop_info->ship, 0, ".", ",") . "đ" }}
                    <strong style="width: 100px;display: inline-block;margin-left:50px">Voucher:</strong> {{ number_format($shop_info->voucher, 0, ".", ",") . "đ" }}  <button type="submit" id="update-discount-today" class="btn btn-success" style="padding: 2px 10px;margin-left:20px;">Áp dụng Voucher + phí Ship</button></p>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width:40px">No.</th>
                                    <th style="width:100px">User</th>
                                    <th>Món đã đặt</th>
                                    <th style="width:80px">Tổng tiền</th>
                                    <th style="width:150px">Ghi chú</th>
                                    <th style="width:120px">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($count_order=0)
                                @foreach ($orders as $order)
                                    @if ($order->status->column_name == 'cancel')
                                    <tr style="display:none">
                                    @elseif ($order->status->column_name == 'unpaid')
                                    <tr class="list-group-item-danger">
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
                                        <td>
                                        @if ($order->status->column_name == 'order')
                                        <span class="badge light badge-warning">{{ $order->status->name }}</span>
                                        @elseif ($order->status->column_name == 'cancel')
                                        <span class="badge light badge-danger">{{ $order->status->name }}</span>
                                        @elseif ($order->status->column_name == 'unpaid')
                                        <span class="badge light badge-danger">{{ $order->status->name }}</span>
                                        @elseif ($order->status->column_name == 'paid')
                                        <span class="badge light badge-success">{{ $order->status->name }}</span>
                                        @else
                                        <span class="badge light badge-success">{{ $order->status->name }}</span>
                                        @endif
                                    </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr style="color:red;font-weight:bold">
                                    <td></td>
                                    <td></td>
                                    <td>Tổng tiền</td>
                                    <td>{{ number_format($total_amount_discount, 0, ".", ",") . "đ" }}</td>
                                    <td>
                                        @if ($total_amount > 0)
                                        <input type="hidden" name="discount" value="{{ ($shop_info->voucher-$shop_info->ship)/$total_amount }}">
                                        @endif
                                    </td>
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
