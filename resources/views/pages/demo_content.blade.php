@extends('layout')

@section('seo')
<title>{{ $content->seo_title }}</title>
@stop

@section('style')
<link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/ekko-lightbox.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/lightbox-dark.css') }}" rel="stylesheet">
@stop

@section('content')
    <h1>{{ $content->title }}</h1>
    <p><a href="{{ url('/') }}">Home</a></p>
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
<script src="{{ asset('assets/js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/ekko-lightbox.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(e) {
        e.preventDefault();
        return $(this).ekkoLightbox();
    });
</script>
@stop
