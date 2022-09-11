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
                    <stepper-order order_type="{{ isset($_GET['order_type']) ? $_GET['order_type'] : '' }}"></stepper-order>

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
                    @foreach ($orders as $order)
                        @if ($order->status->column_name != 'cancel')
                            @php($total_amount+=$order->amount)
                        @endif
                        <input type="hidden" name="order_id[]" value="{{ $order->id }}">
                    @endforeach
                    @if ($total_amount > 0)
                        <input type="hidden" name="discount" value="{{ ($shop_info->voucher-$shop_info->ship)/$total_amount }}">
                    @endif
                    </form>
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
                                    @if ($user->is_admin)
                                        <th>Option</th>
                                    @endif
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
                                        @php($total_amount_discount+=$order->amount-$order->discount)
                                        @php($count_order++)
                                    @endif
                                        <td>{{ $count_order }}</td>
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
                                        <td>{{ Str::words($order->instructions, 50) }}</td>
                                        <td>{!! html_order_status($order->status->column_name, $order->status->name, (isset($order->order_types, $order->history_payments) && $order->order_types->pay_type == 1) ? 1 : 0) !!}</td>
                                    @if ($user->is_admin)
                                    <td>
                                        @if ($order->status->column_name != 'paid')
                                        <form class="form-horizontal" id="form-order-{{$order->id}}" method="post" action="{{ route('admin.orders.update', $order) }}">
                                            {{ csrf_field() }}
                                            {!! method_field('patch') !!}
                                            <input type="hidden" name="status_id" value="4">
                                            <button type="submit" id="update-order-{{$order->id}}" class="btn btn-danger">Thanh Toán</button>
                                        </form>
                                        @endif
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
                                    <td>{{ number_format($total_amount_discount, 0, ".", ",") . "đ" }}</td>
                                    <td></td>
                                    <td></td>
                                    @if ($user->is_admin)
                                        <td></td>
                                    @endif
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
