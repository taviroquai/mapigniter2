
@extends('admin/layout')

@section('style')
<link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>{{ trans('backoffice.visits') }}</h1>

        <table id="example" class="display table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>{{ trans('backoffice.last_visit') }}</th>
                    <th>{{ trans('backoffice.ip') }}</th>
                    <th>{{ trans('backoffice.path') }}</th>
                    <th>{{ trans('backoffice.content') }}</th>
                    <th>{{ trans('backoffice.user') }}</th>
                    <th>{{ trans('backoffice.visits') }}</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th>{{ trans('backoffice.last_visit') }}</th>
                    <th>{{ trans('backoffice.ip') }}</th>
                    <th>{{ trans('backoffice.path') }}</th>
                    <th>{{ trans('backoffice.content') }}</th>
                    <th>{{ trans('backoffice.user') }}</th>
                    <th>{{ trans('backoffice.visits') }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@stop

@section('script')
<script src=" {{ asset('assets/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    
    $('#example').dataTable({
        "ajax": "{{ url('admin/visits') }}",
        "columns": [
            { "data": "created_at" },
            { "data": "ip" },
            { 
                "render": function ( data, type, full, meta ) {
                    return '<a href="' + full.http_path + '" target="_blank">' + full.http_path + '</a>';
                } 
            },
            {
                "render": function ( data, type, full, meta ) {
                    return full.content ? 
                        '<a href="{{ url('admin/contents/form') }}/' 
                        + full.content.id + '">' + full.content.title 
                        + '</a>' 
                    : '';
                }
            },
            {
                "render": function ( data, type, full, meta ) {
                    return full.user ? 
                        '<a href="{{ url('admin/users/form') }}/' 
                        + full.user.id + '">' + full.user.name 
                        + '</a>' 
                    : 'Anonymous';
                }
            },
            { "data": "visits" }
        ]
    });


</script>
@stop
