@extends('layouts.app')

@section('content')
<order-create-component url_shopeefood="https://shopeefood.vn/ho-chi-minh/ni-map-com-tam-com-van-phong" ship_fee="10,000đ" voucher="20,000đ" title="Add Order" alert="{{ $message_order }}"></order-create-component>
@endsection