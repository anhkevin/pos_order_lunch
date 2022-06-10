@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Add Order</div>
                <div style="padding: 10px;">
                    <span><strong>Quán: </strong></span> <span style="color: #c82222;font-weight: bold;">{{ $shop->name }}</span>
                </div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <order-alert user_id="{{ auth()->user()->id }}"></order-alert>

                    <div class="row">
                        <div class="col-lg-12">
                            <form method="post" action="{{ route('user.orders.store') }}" class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="form-group"><label class="col-sm-2 control-label">Đặt cơm</label>

                                    @if ($product_rice->count() > 0)
                                        <div class="col-sm-10 row-no-gutters">
                                            @foreach ($product_rice as $product)
                                                <div class="col-sm-6"><label> <input type="radio" value="{{ $product->id }}" id="product_{{ $product->id }}" name="product_rice"> {{ $product->name }} <span style="color: #ff4d4d;">({{ number_format($product->price, 0, ".", ",") . "đ" }})</span> </label></div>
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
                                            <input type="checkbox" name="toppings[]" value="{{ $product->id }}" id="product_{{ $product->id }}"> {{ $product->name }} <span style="color: #ff4d4d;">({{ number_format($product->price, 0, ".", ",") . "đ" }})</span>
                                        </label></div>
                                        @endforeach
                                    </div>
                                    @endif
                                </div>
                                <div class="hr-line-dashed"></div>

                                <div class="hr-line-dashed"></div>
                                <div class="form-group"><label class="col-sm-2 control-label">Ghi chú</label>

                                    <div class="col-sm-10"><input type="text" name="instructions" class="form-control"></div>
                                </div>

                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                    <div class="col-sm-4 col-sm-offset-2">
                                        <button class="btn btn-success" type="submit">Đặt cơm</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
