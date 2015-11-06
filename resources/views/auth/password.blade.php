<!-- resources/views/auth/login.blade.php -->

@extends('layout')

@section('seo')
<title>Recovery</title>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>Reset Password</h1>
        
        <form method="POST" action="{{ url('password/reset') }}">
            
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="loginEmail">Email</label>
                <input class="form-control" type="email" name="email" id="loginEmail" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">Reset Password</button>
            </div>
        </form>
        
    </div>
</div>
@stop