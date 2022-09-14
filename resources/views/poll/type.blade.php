@extends('layouts.app')

@section('content')
  <div class="d-sm-flex d-block justify-content-between align-items-center mb-4">
    <h2 class="text-black font-w600 mb-sm-0 mb-2">{{ $poll_info->order_name }}</h2>
  </div>
  <div class="row">
    <div class="col-xl-8">
      <div class="row">
        <div class="col-xl-12">
        @php($is_show_btn_join=true)
        @if ($list_staff->count() == 0)
                    <p>No one has joined yet :(</p>
                @else

            <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th class="center">#</th>
                  <th>Name</th>
                  <th>Tiền phải đóng</th>
                  <th class="center">Status</th>
                  <th class="right">Option</th>
                </tr>
              </thead>
              <tbody>
              @php($stt=0)
              @foreach ($list_staff as $staff)
              @if ($staff->status->column_name != 'cancel')
              @php($stt++)
              @if (auth()->user()->id == $staff->user_id)
              @php($is_show_btn_join=false)
              @endif
                <tr>
                  <td class="center">{{ $stt }}</td>
                  <td class="left strong">{{ $staff->user_info->name }}</td>
                  <td class="left">{{ number_format($poll_info->price_every_order, 0, ".", ",") . " đ" }}</td>
                  <td class="center" id="text_status_{{ $staff->id }}">{!! html_poll_status($staff->status->column_name, $staff->status->name, isset($staff->history_payments) ? 1 : 0) !!}</td>
                  <th class="right">
                    @if (auth()->user()->id == $staff->user_id && auth()->user()->id != $staff->assign_user_id && in_array($staff->status->column_name, array('booked', 'unpaid')))
                      <pay-order-type order_id="{{ $staff->id }}"></pay-order-type>
                    @endif
                  </th>
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

    <div class="col-xl-4">
      <div class="row">
        <div class="col-xl-12">
          <div class="card">
                  <div class="card-header border-0 pb-0">
                    <div>
                      <h4 class="fs-20 text-black">Thông Tin Chi Tiết</h4>
                      <div class="fs-14" style="white-space: pre-wrap;">{{ $poll_info->description }}</div>
                      @if ($is_show_btn_join)
                        <join-poll poll_id="{{ base64_encode($poll_info->id) }}"></join-poll>
                      @endif
                  </div>
                </div>
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection