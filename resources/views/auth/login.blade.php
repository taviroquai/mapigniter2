<!-- resources/views/auth/login.blade.php -->

@extends('layout')

@section('seo')
<title>{{ trans('layout.title_login') }}</title>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>{{ trans('layout.title_login') }}</h1>
        
        <form method="POST" action="{{ url('/auth/login') }}">
            
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="loginEmail">{{ trans('layout.label_email') }}</label>
                <input class="form-control" type="email" name="email" id="loginEmail" value="{{ old('email') }}">
                <span class="help-block alert-danger">{{ $errors->first('email') }}</span>
            </div>

            <div class="form-group">
                <label for="loginPassword">{{ trans('layout.label_password') }}</label>
                <input class="form-control" type="password" name="password" id="loginPassword">
                <span class="help-block alert-danger">{{ $errors->first('password') }}</span>
            </div>

            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="remember"> {{ trans('layout.label_rememberme') }}
                </label>
                <br /><a href="{{ url('password/email') }}">{{ trans('layout.link_resetpassword') }}</a>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ trans('layout.btn_login') }}</button>
                <a class="btn btn-info" href="{{ url('ldap/login') }}">{{ trans('layout.link_login_ldap') }}</a>
                <a class="btn btn-warning" href="{{ url('/') }}">{{ trans('layout.link_cancel') }}</a>
            </div>
        </form>
        
    </div>
</div>
@stop