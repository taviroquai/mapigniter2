
@extends('admin/layout')

@section('style')
<link href="{{ asset('assets/css/datepicker3.css') }}" rel="stylesheet" type="text/css">
<link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>{{ trans('backoffice.dashboard') }}</h1>

        <h2>{{ trans('backoffice.visits') }}</h2>
        <form id="formVisits" method="POST" action="{{ url('/admin/dashboard') }}" class="form-inline pull-right">
            <div class="form-group">
                <label for="visits_start">{{ trans('backoffice.from') }}</label>
                <input class="form-control" type="text" name="visits_start" id="visits_start" value="{{ $visits_start }}">
            </div>
            <div class="form-group">
                <label for="visits_end">{{ trans('backoffice.to') }}</label>
                <input class="form-control" type="text" name="visits_end" id="visits_end" value="{{ $visits_end }}">
            </div>
            <button type="submit" class="btn btn-primary">{{ trans('backoffice.apply') }}</button>
        </form>
        <div class="clearfix"></div>
        
        <div id="visitsChart" style="height:300px" ></div>

        <div class="row">
            <div class="col-md-6">
                
                <h2>{{ trans('backoffice.most_visited_content') }}</h2>
                <table id="example" class="display table-striped" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>{{ trans('backoffice.title') }}</th>
                            <th class="col-md-1">{{ trans('backoffice.visits') }}</th>
                        </tr>
                    </thead>

                    @foreach($most_content as $item)
                    <tr>
                        <td>
                            <a href="{{ url('admin/content/'.$item->id) }}">
                                {{ $item->title }}
                            </a>
                        </td>
                        <td>{{ $item->total }}</td>
                    </tr>
                    @endforeach

                </table>

            </div>
            <div class="col-md-6">
                
                <h2>{{ trans('backoffice.less_visited_content') }}</h2>
                <table id="example" class="display table-striped" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>{{ trans('backoffice.title') }}</th>
                            <th class="col-md-1">{{ trans('backoffice.visits') }}</th>
                        </tr>
                    </thead>

                    @foreach($less_content as $item)
                    <tr>
                        <td>
                            <a href="{{ url('admin/content/'.$item->id) }}">
                                {{ $item->title }}
                            </a>
                        </td>
                        <td>{{ $item->total }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
        
    </div>
</div>
@stop

@section('script')
<script src="{{ asset('assets/js/bootstrap-datepicker.js') }}"></script>
<script src="{{ asset('assets/js/jquery.sparkline.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.flot.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.flot.time.min.js') }}"></script>
<script type="text/javascript">
    
    var date_options = {
        format: 'yyyy-mm-dd',
        autoclose: true
    };
    $('[name="visits_start"]').datepicker(date_options);
    $('[name="visits_end"]').datepicker(date_options);
    
    // Init visits chart
    $('#formVisits').on('submit', function (e) {
        e.preventDefault();
        updateVisitsChart($('[name="visits_start"]').val(), $('[name="visits_end"]').val());
        return false;
    });
    updateVisitsChart($('[name="visits_start"]').val(), $('[name="visits_end"]').val());
    
    function updateVisitsChart(date_start, date_end) {
        $.getJSON('{{ url("admin/visits/totals") }}/' + date_start + '/' + date_end, function (r) {
            initVisitsChart("#visitsChart", r.data, date_start, date_end);
        });
    }
    
    function initVisitsChart(el, data, date_start, date_end) {
        $.plot($(el), [ { data: data, label: "{{ trans('backoffice.visits') }}"} ], {
            series: {
                lines: { show: true,
                         lineWidth: 2,
                         fill: true, fillColor: { colors: [ { opacity: 0.5 }, { opacity: 0.2 } ] }
                      },
                points: { show: true, 
                          lineWidth: 2 
                      },
                shadowSize: 0
            },
            grid: { hoverable: true, 
                    clickable: true, 
                    tickColor: "#f9f9f9",
                    borderWidth: 0
                  },
            colors: ["#3B5998"],
             xaxis: {
                 min: new Date(date_start), max: new Date(date_end),
                 mode: "time"
             },
             yaxis: {ticks:3, tickDecimals: 0},
        });

        function showTooltip(x, y, contents) {
            $('<div id="tooltip">' + contents + '</div>').css( {
                position: 'absolute',
                display: 'none',
                top: y + 5,
                left: x + 5,
                border: '1px solid #fdd',
                padding: '2px',
                'background-color': '#dfeffc',
                opacity: 0.80
            }).appendTo("body").fadeIn(200);
        }

        var previousPoint = null;
        $(el).bind("plothover", function (event, pos, item) {
            $("#x").text(pos.x.toFixed(2));
            $("#y").text(pos.y.toFixed(2));

                if (item) {
                    if (previousPoint != item.dataIndex) {
                        previousPoint = item.dataIndex;

                        $("#tooltip").remove();
                        var x = item.datapoint[0].toFixed(2),
                            y = item.datapoint[1].toFixed(2),
                            tdate = new Date(parseInt(x));
                        var months = {!! trans('backoffice.js_months_array') !!};
                        var year = tdate.getFullYear();
                        var month = months[tdate.getMonth()];
                        var date = tdate.getDate();
                        var x_string = year + '-' + month + '-' + date;
                        showTooltip(item.pageX, item.pageY, item.series.label + " of " + x_string + " = " + y);
                    }
                }
                else {
                    $("#tooltip").remove();
                    previousPoint = null;
                }
        });
    }
</script>
@stop
