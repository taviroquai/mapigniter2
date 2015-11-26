<!-- resources/views/auth/reset.blade.php -->

@extends('layout')

@section('seo')
<title>{{ trans('layout.title_recovery') }}</title>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>{{ trans('layout.title_recovery') }}</h1>
        
        <form method="POST" action="{{ url('/password/reset') }}">
            
            {!! csrf_field() !!}
            
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="loginEmail">{{ trans('layout.label_email') }}</label>
                <input class="form-control" type="email" name="email" id="loginEmail" value="{{ old('email') }}">
                <span class="help-block alert-danger">{{ $errors->first('email') }}</span>
            </div>

            <div>
                <label for="registerPassword">{{ trans('layout.label_password') }}</label>
                <input class="form-control" type="password" name="password" id="registerPassword">
                <span class="help-block alert-danger">{{ $errors->first('password') }}</span>
            </div>

            <div>
                <label for="registerPassword2">{{ trans('layout.label_confirmpassword') }}</label>
                <input class="form-control" type="password" name="password_confirmation"  id="registerPassword2">
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">
                    {{ trans('layout.btn_resetpassword') }}
                </button>
            </div>
        </form>
    
    </div>
</div>
@stop