@extends('layout')

@section('seo')
<title>{{ $brand->name }}</title>
@stop

@section('content')
    <h1>{{ $brand->name }}</h1>
    <img class="col-md-2 thumbnail" src="{{ $brand->getPictureUrl() }}" alt="Logo" />

    <div class="clearfix"></div>
    <h2>Demo Pages</h2>
    <ul>
        <li><a href="{{ url('demo/events') }}">Demo Events</a></li>
        <li><a href="{{ url('demo/map') }}">Demo Map</a></li>
        @foreach($contents as $item)
        <li><a href="{{ url($item->seo_slug) }}">{{ $item->title }}</a></li>
        @endforeach
    </ul>

    @if (!Auth::check())
            <a href="{{ url('auth/login') }}">Login</a>
            <a href="{{ url('ldap/login') }}">LDAP Login</a>
            <a href="{{ url('auth/register') }}">Register</a>
    @else
            <a href="{{ url('admin/dashboard') }}">Admin</a>
            <a href="{{ url('auth/logout') }}">Logout</a>
    @endif

@stop