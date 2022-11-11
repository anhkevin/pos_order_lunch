@extends('layouts.app')

@section('content')

<div class="mb-3">
    <div class="d-md-flex align-items-center justify-content-between">
        <div>
            @if ($list_order_type->count() > 1)
                @foreach ($list_order_type as $order_type)
                    @if ((empty($order_column) && $order_type->is_default == 1) || (!empty($order_column) && $order_column == $order_type->column_name))
                        <a href="{{ route('user.orders.create_column', $order_type->column_name) }}" class="btn btn-primary mr-2 mb-1">{{ $order_type->order_name }}</a>
                    @else
                        <a href="{{ route('user.orders.create_column', $order_type->column_name) }}" class="btn btn-dark mr-2 mb-1">{{ $order_type->order_name }}</a>
                    @endif
                @endforeach
            @endif
        </div>
        <add-order-type></add-order-type>
    </div>
</div>

<order-create-component url_shopeefood="{{ $shop->address }}" shop_info_id="{{ $shop->id }}" shop_info_close="{{ $is_close_order }}" ship_fee="{{ $shop->ship }}" voucher="{{ $shop->voucher }}" order_column="{{ $order_column }}" title="{{ $shop->name }}" shop_type_id="{{ $shop_type_id }}" alert="{{ $message_order }}" is_admin="{{ $user->is_admin }}"></order-create-component>

@endsection