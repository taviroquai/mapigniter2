@extends('layout')

@section('style')
<link href="{{ asset('assets/css/calendar.css') }}" rel="stylesheet" type="text/css" />
<style type="text/css">
    #cal-slide-content a.event-item {
        color: black;
    }
</style>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        
        <h1>Demo Events</h1>
        <p><a href="{{ url('/') }}">Home</a></p>
        <div class="clearfix"></div>
        
        <div class="pull-right form-inline">
			<div class="btn-group">
				<button class="btn btn-primary" data-calendar-nav="prev"><< Previous</button>
				<button class="btn" data-calendar-nav="today">Today</button>
				<button class="btn btn-primary" data-calendar-nav="next">Next >></button>
			</div>
            <div class="btn-group">
				<button class="btn btn-warning" data-calendar-view="month">Month View</button>
			</div>
		</div>
        <div class="clearfix"></div>

        <div id="calendar"></div>
    </div>
</div>
@stop

@section('script')
<script src="{{ asset('assets/js/jquery.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/underscore-min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/calendar.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(function() {
        var calendar = $("#calendar").calendar({
            tmpl_path: "{{ url('assets/templates') }}/",
            events_source: events
        });
        $('.btn-group button[data-calendar-nav]').each(function() {
            var $this = $(this);
            $this.click(function() {
                calendar.navigate($this.data('calendar-nav'));
            });
        });
        $('.btn-group button[data-calendar-view]').each(function() {
            var $this = $(this);
            $this.click(function() {
                calendar.view($this.data('calendar-view'));
            });
        });
    });
    var events = JSON.parse('{!! $json !!}');
    console.log(events);
</script>
@stop