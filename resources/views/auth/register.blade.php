@extends('layouts.app')

<div class="authincation h-100">
    <div class="container h-100">
        <div class="row justify-content-center h-100 align-items-center">
			<div class="col-md-6">
        
                <div class="authincation-content">
                    <div class="row no-gutters">
                        <div class="col-xl-12">
                            <div class="auth-form">
                                <h2 class="text-center mb-4 text-white">REGISTER</h2>
                                <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                                    {{ csrf_field() }}
                                    <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                        <label class="mb-1 text-white"><strong>Name</strong></label>
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label class="mb-1 text-white"><strong>Email</strong></label>
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label class="mb-1 text-white"><strong>Password</strong></label>
                                        <input id="password" type="password" class="form-control" name="password" required>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                    <!-- <div class="form-group">
                                        <label class="mb-1 text-white"><strong>Confirm Password</strong></label>
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                    </div> -->
                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn bg-white text-primary btn-block">Register</button>
                                    </div>
                                </form>
                                <div class="new-account mt-3">
                                    <p class="text-white">Already have an account? <a class="text-white" href="{{ route('login') }}">Sign in</a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>