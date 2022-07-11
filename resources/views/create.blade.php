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

                    <div class="row">
                        
                        <div class="col-md-12">
                        
                                <form method="post" action="{{ route('user.orders.store') }}" class="form-horizontal">
                                {{ csrf_field() }}

                                <div class="hr-line-dashed"></div>
                                <div class="form-group">
                                @if (session('status'))
                                    <div class="alert alert-danger">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Ghi chú</label>
                                    <div class="col-sm-3"><input type="text" name="instructions" class="form-control" style="width: auto;"></div>
                                    <div class="col-sm-3">
                                        @if (!empty($message_order))
                                            <button class="btn btn-success" disabled type="submit">Đặt cơm</button> 
                                            <span class="alert alert-danger">( {{ $message_order }} )</span>
                                        @else
                                            <button class="btn btn-success" type="submit">Đặt cơm</button> 
                                        @endif
                                    </div>
                                </div>

                                <div class="hr-line-dashed"></div>
                                <br>

                                <ul class="list-group">
                                    @foreach ($product_rice as $key_product => $product)
                                    <li class="list-group-item" style="width: 50%;display: inline-block;float: left;">
                                        <h4>{{ $key_product }}</h4>
                                        @foreach ($product as $value)
                                        <div>
                                            <label class="row" style="display: block;margin-bottom: 10px;display: flex;align-items: center;border: 1px solid #bbb;" for="product_{{ $value->id }}">
                                                <div class="col-md-1">
                                                    @if ($product_first->dish_type_name == $value->dish_type_name)
                                                        <input type="radio" value="{{ $value->id }}" id="product_{{ $value->id }}" name="product_rice">
                                                    @else
                                                        <input type="checkbox" name="toppings[]" value="{{ $value->id }}" id="product_{{ $value->id }}">
                                                    @endif
                                                </div>
                                                <div class="col-md-3">
                                                    <div style="width: 70px;"><img src="{{ $value->dish_photo }}" alt="" style="width: 100%; border-radius: 5px;"></div>
                                                </div>
                                                <div class="col-md-8">
                                                    <h4><strong>{{ $value->name }}</strong></h4>
                                                    <p>
                                                        <span style="color: red; font-size: 18px;">
                                                            ({{ number_format($value->price, 0, ".", ",") . "đ" }})
                                                        </span>
                                                    </p>
                                                </div>
                                            </label>
                                        </div>
                                        @endforeach
                                    </li>
                                    @endforeach
                                </ul>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
