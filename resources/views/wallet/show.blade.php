@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="container-fluid">
                    <div class="form-head mb-4">
                        <h2 class="text-black font-w600 mb-0">Danh sách Wallet</h2>
                    </div>
                    <div class="row">
                        <div class="col-xl-9 col-xxl-12">
                        <div class="row">
                            <div class="col-xl-12">
                                <show-wallet-component is_admin="{{auth()->user()->is_admin}}"></show-wallet-component>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
