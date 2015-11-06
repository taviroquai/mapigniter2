<!-- resources/views/auth/login.blade.php -->

@extends('layout')

@section('seo')
<title>Login</title>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>Login</h1>
        
        <form method="POST" action="{{ url('/auth/login') }}">
            
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input class="form-control" type="email" name="email" id="loginEmail" value="{{ old('email') }}">
                <span class="help-block alert-danger">{{ $errors->first('email') }}</span>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input class="form-control" type="password" name="password" id="loginPassword">
                <span class="help-block alert-danger">{{ $errors->first('password') }}</span>
            </div>

            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
                <br /><a href="{{ url('password/email') }}">Reset Password</a>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">Login</button>
                <a class="btn btn-info" href="{{ url('ldap/login') }}">{{ trans('layout.link_login_ldap') }}</a>
            </div>
        </form>
        
    </div>
</div>
@stop