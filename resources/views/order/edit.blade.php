@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="card">
            <div class="card-header">Edit Order</div>

            <div class="card-body">
                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif

                <order-alert user_id="{{ auth()->user()->id }}"></order-alert>

                <div class="row">
                    <div class="col-lg-12">
                        <form method="post" action="{{ route('user.orders.update', $order) }}" class="form-horizontal">
                            {{ csrf_field() }}

                            <div class="form-group"><label class="col-sm-2 control-label">Đặt cơm</label>

                                @if ($product_rice->count() > 0)
                                    <div class="col-sm-10">
                                        @foreach ($product_rice as $product)
                                            <div><label> 
                                                @if (in_array($product->id, $arr_product_id))
                                                <input type="radio" value="{{ $product->id }}" id="product_{{ $product->id }}" name="product_rice" checked >
                                                @else
                                                <input type="radio" value="{{ $product->id }}" id="product_{{ $product->id }}" name="product_rice">
                                                @endif
                                                    {{ $product->name }} <span style="color: #ff4d4d;">({{ number_format($product->price, 0, ".", ",") . "đ" }})</span> </label></div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group"><label class="col-sm-2 control-label">Món thêm</label>
                                @if ($product_option->count() > 0)
                                <div class="col-sm-10">
                                    @foreach ($product_option as $product)
                                    <div><label class="checkbox-inline">
                                        @if (in_array($product->id, $arr_product_id))
                                            <input type="checkbox" name="toppings[]" value="{{ $product->id }}" id="product_{{ $product->id }}" checked >
                                        @else
                                            <input type="checkbox" name="toppings[]" value="{{ $product->id }}" id="product_{{ $product->id }}">
                                        @endif
                                            {{ $product->name }} <span style="color: #ff4d4d;">({{ number_format($product->price, 0, ".", ",") . "đ" }})</span>
                                    </label></div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            <div class="hr-line-dashed"></div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group"><label class="col-sm-2 control-label">Ghi chú</label>

                                <div class="col-sm-10"><input type="text" name="instructions" value="{{ $order->instructions }}" class="form-control"></div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-2">
                                    <button class="btn btn-success" type="submit">Update</button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
