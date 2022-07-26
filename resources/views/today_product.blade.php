@extends('layouts.app')

@section('content')
<div class="">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <div class="card">
                <div class="card-header">Danh sách món đã order ngày <span style="color:red">{{ date("Y/m/d") }}</span></div>

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
