@extends('layouts.app')

@section('content')
<div class="">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                <h2 class="text-black font-w600 mb-0">Danh sách món đã Order @if ($title)<small>({{ $title }})</small>@endif</h2></div>
                
                <div class="card-body bootstrap-badge" style="padding-top: 0;">
                    @if ($list_order_type->count() > 1)
                        @foreach ($list_order_type as $order_type)
                            @if ((empty($_GET['order_type']) && $order_type->is_default == 1) || (!empty($_GET['order_type']) && $_GET['order_type'] == base64_encode($order_type->id)))
                                <a href="{{ route('user.orders.product') }}?order_type={{ base64_encode($order_type->id) }}" class="badge badge-primary">{{ $order_type->order_name }}</a>
                            @else
                                <a href="{{ route('user.orders.product') }}?order_type={{ base64_encode($order_type->id) }}" class="badge badge-dark">{{ $order_type->order_name }}</a>
                            @endif
                        @endforeach
                    @endif
                </div>

                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-success">
                            {{ session('message') }}
                        </div>
                    @endif

                @if (count($products_rice) == 0)
                    <p>No orders yet.</p>
                    <a class="btn btn-success" href="{{ route('user.orders.create') }}">Add Order</a>

                @else

                    <order-alert user_id="{{ auth()->user()->id }}"></order-alert>
                    @php($total_amount=0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Món</th>
                                    <th>số lượng</th>
                                    <th>Tổng tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($products_rice as $key_product => $products)
                                <tr style="color:red;font-weight:bold">
                                    <td>{{ $key_product }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @foreach ($products as $product)
                                    @php($total_amount+=($product->count_product * $product->price))
                                    <tr style="color: black;font-weight: 600;">
                                        <td>{{ $product->product_name }}</td>
                                        <td>{{ $product->count_product }}</td>
                                        <td>{{ number_format($product->count_product * $product->price, 0, ".", ",") . "đ" }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                                
                            </tbody>
                            <tfoot>
                                <tr style="color:red;font-weight:bold">
                                    <td></td>
                                    <td></td>
                                    <td>{{ number_format($total_amount, 0, ".", ",") . "đ" }}</td>
                                </tr>
                            </tfood>

                        </table>
                    </div> <!-- end table-responsive -->

                @endif

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
