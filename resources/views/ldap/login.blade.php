<!-- resources/views/auth/login.blade.php -->

@extends('layout')

@section('seo')
<title>LDAP Login</title>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>LDAP Login</h1>
        
        @if(\Session::has('status'))
            <div class="alert alert-danger" role="alert">{{ \Session::get('status') }}</div>
        @endif
        
        <form method="POST" action="{{ url('/ldap/login') }}">
            
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="loginUsername">Username</label>
                <input class="form-control" type="text" required
                    name="username" id="loginUsername" value="{{ old('username') }}">
                <span class="help-block alert-danger">{{ $errors->first('username') }}</span>
            </div>

            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input class="form-control" type="password" required
                    name="password" id="loginPassword">
                <span class="help-block alert-danger">{{ $errors->first('password') }}</span>
            </div>

            <div class="form-group checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember Me
                </label>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">Login</button>
            </div>
        </form>
        
    </div>
</div>
@stop