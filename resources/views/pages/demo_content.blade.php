@extends('ajax')

@section('seo')
<title>{{ $content->seo_title }}</title>
@stop

@section('style')
@stop

@section('content')
    <h1>{{ $content->title }}</h1>
    
    <p><img class="col-md-2 thumbnail" style="margin: 20px" src="{{ $content->getPictureUrl() }}" alt="Logo" /></p>
    {!! $content->getContent() !!}
    
    <div class="clearfix"></div>
    <h2>Content Gallery</h2>
    <div class="row">
        @foreach($content->getGalleryImages() as $item)
        <div class="col-md-2">
            <a href="{{ $content->getGalleryImageUrl($item) }}" data-toggle="lightbox" data-gallery="multiimages" data-title="{{ $content->title }}" class="thumbnail">
                <img src="{{ $content->getGalleryImageUrl($item) }}" />
            </a>
        </div>
        @endforeach
    </div>
    <h2>Attachments</h2>
    <ul class="list-group">
        @foreach($content->getAttachments() as $item)
        <li class="list-group-item">
            <a href="{{ $content->getAttachmentUrl($item) }}">
                {{ basename($item) }}
            </a>
        </li>
        @endforeach
    </ul>
@stop

@section('script')
@stop
