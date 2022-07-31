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
<div class="bootstrap-badge">
    @if ($list_order_type->count() > 1)
        @foreach ($list_order_type as $order_type)
            @if ((empty($_GET['order_type']) && $order_type->is_default == 1) || (!empty($_GET['order_type']) && $_GET['order_type'] == base64_encode($order_type->id)))
                <a href="{{ route('user.orders.create') }}?order_type={{ base64_encode($order_type->id) }}" class="badge badge-primary">{{ $order_type->order_name }}</a>
            @else
                <a href="{{ route('user.orders.create') }}?order_type={{ base64_encode($order_type->id) }}" class="badge badge-dark">{{ $order_type->order_name }}</a>
            @endif
        @endforeach
    @endif
</div>
<order-create-component url_shopeefood="{{ $shop->address }}" ship_fee="{{ $shop->ship }}" voucher="{{ $shop->voucher }}" title="" shop_type_id="{{ $shop_type_id }}" alert=""></order-create-component>
@endsection