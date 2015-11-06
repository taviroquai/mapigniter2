<!-- resources/views/auth/register.blade.php -->

@extends('layout')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Register</h1>

        <form method="POST" action="{{ url('/auth/register') }}">
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="registerName">Name</label>
                <input class="form-control" type="text" name="name" id="registerName" value="{{ old('name') }}">
                <span class="help-block alert-danger">{{ $errors->first('name') }}</span>
            </div>

            <div class="form-group">
                <label for="registerEmail">Email</label>
                <input class="form-control" type="email" name="email" id="registerEmail" value="{{ old('email') }}">
                <span class="help-block alert-danger">{{ $errors->first('email') }}</span>
            </div>

            <div class="form-group">
                <label for="registerPassword">Password</label>
                <input class="form-control" type="password" name="password" id="registerPassword">
                <span class="help-block alert-danger">{{ $errors->first('password') }}</span>
            </div>

            <div class="form-group">
                <label for="registerPassword2">Confirm Password</label>
                <input class="form-control" type="password" name="password_confirmation"  id="registerPassword2">
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">Register</button>
            </div>
        </form>
    </div>
</div>
@stop