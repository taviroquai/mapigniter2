<!-- resources/views/auth/login.blade.php -->

@extends('layout')

@section('seo')
<title>{{ trans('layout.title_ldap_login') }}</title>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>{{ trans('layout.title_ldap_login') }}</h1>
        
        @if(\Session::has('status'))
            <div class="alert alert-danger" role="alert">{{ \Session::get('status') }}</div>
        @endif
        
        <form method="POST" action="{{ url('/ldap/login') }}">
            
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="loginUsername">{{ trans('layout.label_username') }}</label>
                <input class="form-control" type="text" required
                    name="username" id="loginUsername" value="{{ old('username') }}">
                <span class="help-block alert-danger">{{ $errors->first('username') }}</span>
            </div>

            <div class="form-group">
                <label for="loginPassword">{{ trans('layout.label_password') }}</label>
                <input class="form-control" type="password" required
                    name="password" id="loginPassword">
                <span class="help-block alert-danger">{{ $errors->first('password') }}</span>
            </div>

            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="remember"> {{ trans('layout.label_rememberme') }}
                </label>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ trans('layout.btn_login') }}</button>
                <a class="btn btn-warning" href="{{ url('auth/login') }}">{{ trans('layout.link_cancel') }}</a>
            </div>
        </form>
        
    </div>
</div>
@stop