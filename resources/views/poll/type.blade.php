@extends('layouts.app')

@section('content')
  <div class="d-sm-flex d-block justify-content-between align-items-center mb-4">
    <h2 class="text-black font-w600 mb-sm-0 mb-2">{{ $poll_info->order_name }} 
    @if (auth()->user()->is_admin || auth()->user()->id == $poll_info->assign_user_id)
      <btn-edit-poll poll_id="{{ $poll_info->id }}" poll_name="{{ $poll_info->order_name }}" poll_description="{{ $poll_info->description }}" poll_money="{{ $poll_info->price_every_order }}"></btn-edit-poll>
    @endif
    </h2>
  </div>
  <div class="row">
    <div class="col-xl-9">
      <div class="row">
        <div class="col-xl-12">
        @if ($list_staff->count() == 0)
                    <p>No one has joined yet :(</p>
                @else

            <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th class="center">#</th>
                  <th>Name</th>
                  @if ($poll_info->price_every_order > 0)
                  <th>Tiền phải đóng</th>
                  @endif
                  <th class="center">Status</th>
                  <th class="right">Option</th>
                </tr>
              </thead>
              <tbody>
              @php($stt=0)
              @foreach ($list_staff as $staff)
              @if ($staff->status->column_name != 'cancel')
              @php($stt++)
                <tr>
                  <td class="center">{{ $stt }}</td>
                  <td class="left strong">{{ $staff->address }}</td>
                  @if ($poll_info->price_every_order > 0)
                  <td class="left">{{ number_format($staff->amount, 0, ".", ",") . " ₫" }}</td>
                  @endif
                  <td class="center" id="text_status_{{ $staff->id }}">{!! html_poll_status($staff->status->column_name, $staff->status->name, isset($staff->history_payments) ? 1 : 0) !!}</td>
                  <td class="right">
                    @if (auth()->user()->id == $staff->user_id && auth()->user()->id != $staff->assign_user_id && in_array($staff->status->column_name, array('booked', 'unpaid')))
                      <pay-order-type order_id="{{ $staff->id }}"></pay-order-type>
                    @endif
                    @if ($poll_info->price_every_order > 0 && (auth()->user()->is_admin || auth()->user()->id == $staff->assign_user_id) && $staff->status->column_name != 'paid')
                      <admin-pay-order order_id="{{ $staff->id }}" order_name="{{ $staff->address }}"></admin-pay-order>
                    @endif
                    @if ((auth()->user()->is_admin || auth()->user()->id == $staff->assign_user_id || auth()->user()->id == $staff->user_id) && $staff->status->column_name == 'order')
                      <btn-cancel-order order_id="{{ $staff->id }}"></btn-cancel-order>
                    @endif
                  </td>
                </tr>
                @endif
                @endforeach
                
              </tbody>
            </table>
          </div>

          @endif

          </div>
        </div>
      </div>

    <div class="col-xl-3">
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
                  <div class="card-header border-0 pb-0">
                    <div>
                      <h4 class="fs-20 text-black">Thông Tin Chi Tiết</h4>
                      <div class="fs-14" style="white-space: pre-wrap;">{{ $poll_info->description }}</div>
                      <join-poll poll_id="{{ base64_encode($poll_info->id) }}" user_name="{{ Auth::user()->name }}"></join-poll>
                  </div>
                </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection