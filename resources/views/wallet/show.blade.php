@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h2 class="text-black font-w600 mb-0">Danh sách Wallet</h2>
            </div>
            <div class="card-body">
                <show-wallet-component is_admin="{{auth()->user()->is_admin}}"></show-wallet-component>
            </div>
        </div>
    </div>
</div>
@endsection
