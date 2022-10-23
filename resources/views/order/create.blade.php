@extends('layouts.app')

@section('content')

<div class="form-head mb-4">
    <h1 class="text-black font-w600 mb-0 inline-block">Đặt món @if ($title)<small>({{ $title }})</small>@endif</h1>

    <shop_update_status shop_id="{{ $shop->id }}" is_close="{{ $shop->is_close }}"></shop_update_status>
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
@if ($message_order)
    <div class="row justify-content-center h-100 align-items-center">
        <div class="col-12">
            <div class="form-input-content text-center error-page mt-5 pt-5">
                <h3 class="error-text font-weight-bold">{{ $title }}</h3>
                <h4><i class="fa fa-times-circle text-danger"></i> {{ $message_order }}</h4>
            </div>
        </div>
    </div>
@else
    <order-create-component url_shopeefood="{{ $shop->address }}" ship_fee="{{ $shop->ship }}" voucher="{{ $shop->voucher }}" title="" shop_type_id="{{ $shop_type_id }}" alert=""></order-create-component>
@endif
@endsection