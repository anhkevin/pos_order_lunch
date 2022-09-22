@extends('layouts.app')

@section('content')
@if ($message_order)
<div class="alert alert-danger alert-dismissible fade show">
Đơn hàng (<strong style="color: #3f51b5;">{{ $title }}</strong>) {{ $message_order }}
</div>
@endif

<div class="form-head mb-4">
    <h1 class="text-black font-w600 mb-0">Đặt món @if ($title)<small>({{ $title }})</small>@endif</h1>
</div>
<div class="bootstrap-badge pl-3 pt-3">
    @if ($list_order_type->count() > 1)
        @foreach ($list_order_type as $order_type)
            @if ((empty($order_column) && $order_type->is_default == 1) || (!empty($order_column) && $order_column == $order_type->column_name))
                <a href="{{ route('user.orders.create_column', $order_type->column_name) }}" class="badge badge-primary">{{ $order_type->order_name }}</a>
            @else
                <a href="{{ route('user.orders.create_column', $order_type->column_name) }}" class="badge badge-dark">{{ $order_type->order_name }}</a>
            @endif
        @endforeach
    @endif
</div>
<order-create-component url_shopeefood="{{ $shop->address }}" ship_fee="{{ $shop->ship }}" voucher="{{ $shop->voucher }}" title="" shop_type_id="{{ $shop_type_id }}" alert=""></order-create-component>
@endsection