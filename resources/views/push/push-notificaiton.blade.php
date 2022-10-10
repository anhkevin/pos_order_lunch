@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Push Notification</h4>
            </div>
            <div class="card-body">
                <div class="basic-form">
                    <form action="{{ route('send.web-notification') }}" method="POST">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Message Title</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="title">
                            </div>
                            </div>
                            <div class="form-group row">
                            <label class="col-sm-3 col-form-label">Message Body</label>
                            <div class="col-sm-9">
                                <textarea class="form-control" name="body"></textarea>
                            </div>
                            </div>
                            
                            <div class="form-group row">
                            <div class="col-sm-10">
                                <button type="submit" class="btn btn-primary">Send Notification</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
