@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Admin Dashboard</div>

            <div class="card-body">
                @if (session('message'))
                    <div class="alert alert-success">
                        {{ session('message') }}
                    </div>
                @endif

            @if ($orders->count() == 0)
                <p>No orders yet.</p>
                <a class="btn btn-success" href="{{ route('user.orders.create') }}">Add Order</a>

            @else

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width:40px">ID</th>
                                <th style="width:120px">User</th>
                                <th>Cơm</th>
                                <th style="width:120px">Tổng tiền</th>
                                <th style="width: 150px;">Ghi chú</th>
                                <th style="width:80px">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                @if ($order->status->column_name == 'cancel')
                                <tr style="background: #c1c1c1;">
                                @elseif ($order->status->column_name == 'unpaid')
                                <tr class="blink">
                                @else
                                <tr>
                                @endif
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer->name }}</td>
                                    <td>{!! nl2br2(e($order->size)) !!}</td>
                                    <td>
                                        @if ($order->discount > 0)
                                            <span style="text-decoration-line: line-through;">{{ number_format($order->amount, 0, ".", ",") . "đ" }}</span><br>
                                            <span style="color: #bc0c0c;font-weight: bold;">{{ number_format($order->amount-$order->discount, 0, ".", ",") . "đ" }}</span><br>
                                        @else
                                            {{ number_format($order->amount, 0, ".", ",") . "đ" }}
                                        @endif
                                    </td>
                                    <td>{{ Str::words($order->instructions, 50) }}</td>
                                    <td></td>
                                    <td><a href="{{ route('admin.orders.edit', $order) }}">
                                    @if ($order->status->column_name == 'order')
                                                        <span class="badge light badge-warning">{{ $order->status->name }}</span>
                                                        @elseif ($order->status->column_name == 'cancel')
                                                        <span class="badge light badge-danger">{{ $order->status->name }}</span>
                                                        @elseif ($order->status->column_name == 'unpaid')
                                                        <span class="badge light badge-danger">{{ $order->status->name }}</span>
                                                        @elseif ($order->status->column_name == 'paid')
                                                        <span class="badge light badge-success">{{ $order->status->name }}</span>
                                                        @else
                                                        <span class="badge light badge-success">{{ $order->status->name }}</span>
                                                        @endif
                                    </a></td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div> <!-- end table-responsive -->

            @endif




            </div>
        </div>
    </div>
</div>
@endsection
