@extends('layout')

@section('seo')
<title>Demo Locations</title>
@stop

@section('content')
    
    <h2>Locations</h2>
    <p><a href="{{ url('/') }}">Home</a></p>
    <div class="clearfix"></div>
    
    <div id="map" style="width: 100%; height: 380px;"></div>
    
@stop

@section('script')
<script src="http://maps.googleapis.com/maps/api/js" type="text/javascript"></script>
<script type="text/javascript">
    var map, data, marker, info;
    
    var mapOptions = {
        zoom: 2,
        center: new google.maps.LatLng(0, 0),
        mapTypeId: google.maps.MapTypeId.SATELLITE,
        style: google.maps.ZoomControlStyle.LARGE
    };
    map = new google.maps.Map(document.getElementById("map"), mapOptions);
    
    data = [];
    @foreach($locations as $item)
    data.push({
        title: '{{ $item->content->title }}',
        description: '{{ $item->content->seo_description }}',
        lat: {{ $item->lat }},
        lon: {{ $item->lon }}
    });
    @endforeach
    
    for (var i = 0; i < data.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(data[i].lat, data[i].lon),
            title: data[i].title,
            description: data[i].title,
            map: map
        });
        google.maps.event.addListener(marker, 'click', function() {
            info = new google.maps.InfoWindow({
                content: marker.description
            });
            info.open(map, marker);
        });
    }
</script>
@stop
