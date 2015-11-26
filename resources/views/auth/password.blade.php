<!-- resources/views/auth/login.blade.php -->

@extends('layout')

@section('seo')
<title>{{ trans('layout.title_recovery') }}</title>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>{{ trans('layout.title_recovery') }}</h1>
        
        <form method="POST" action="{{ url('password/reset') }}">
            
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="loginEmail">{{ trans('layout.label_email') }}</label>
                <input class="form-control" type="email" name="email" id="loginEmail" value="{{ old('email') }}">
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ trans('layout.btn_resetpassword') }}</button>
                <a class="btn btn-warning" href="{{ url('auth/login') }}">{{ trans('layout.link_cancel') }}</a>
            </div>
        </form>
        
    </div>
</div>
@stop