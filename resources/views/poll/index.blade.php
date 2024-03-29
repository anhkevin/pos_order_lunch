@extends('layouts.app')

@section('content')

    <div class="page-titles">
        <h4>Poll Market</h4>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item active"><a href="{{ route('user.poll.index') }}">Poll Market</a></li>
        </ol>
    </div>

    <div class="row">
        @if ($poll_list->count() > 0)
            @foreach ($poll_list as $key => $poll)
            @if ($key == 0 && strpos($poll->column_name, 'dabanh') !== false)
            <a href="{{ route('user.poll.type', 'dabanh') }}" target="_blank" class="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
            @else
            <a href="{{ route('user.poll.type', $poll->column_name) }}" target="_blank" class="col-xl-3 col-xxl-4 col-lg-6 col-sm-6">
            @endif
                @if ($poll->status_type->column_name == 'cancel' || $poll->order_date < date("Y-m-d"))
                <div class="widget-stat card bg-dark">
                @else
                <div class="widget-stat card bg-primary">
                @endif
                    <div class="card-body  p-4">
                        <div class="media">
                            <span class="mr-3">
                                @if (strpos($poll->column_name, "dabanh") !== false)
                                    <i class="las la-futbol"></i>
                                @else
                                    <i class="la la-users"></i>
                                @endif
                            </span>
                            <div class="media-body text-white">
                                <p class="mb-1 text-black fs-18 font-w600">{{ $poll->order_name }}</p>
                                <h3 class="text-white">{{ count($poll->orders) }} <small>member</small></h3>
                                <p class="mt-1 mb-0 ml-3">
                                    <label class="fs-14 mb-0 op6" style="min-width: 80px;">STATUS: </label><span>{!! label_poll_status($poll->status_type->column_name, $poll->order_date) !!}</span><br>
                                    <label class="fs-14 mb-0 op6" style="min-width: 80px;">DUE DATE: </label><span>{{ $poll->order_date }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        @endif
        
    </div>

@endsection