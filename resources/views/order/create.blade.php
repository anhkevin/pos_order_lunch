@extends('layouts.app')

@section('content')
<order-create-component url_shopeefood="{{ $shop->address }}" ship_fee="{{ $shop->ship }}" voucher="{{ $shop->voucher }}" title="{{ $title }}" alert="{{ $message_order }}"></order-create-component>
@endsection