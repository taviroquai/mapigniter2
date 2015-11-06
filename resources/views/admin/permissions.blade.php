
@extends('admin/layout')

@section('style')
<link href="{{ asset('assets/css/jquery.dataTables.min.css') }}" rel="stylesheet" type="text/css">
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
        
        <h1>{{ trans('backoffice.permissions') }}</h1>
        
        <p class="clearfix pull-right">
            <a class="btn btn-warning" href="{{ url('admin/permissions/download') }}"><i class="fa fa-file-o"></i> {{ trans('backoffice.download_logs') }}</a>
            <a class="btn btn-success" href="{{ url('admin/permissions/form') }}"><i class="fa fa-ban"></i> {{ trans('backoffice.create_permission') }}</a>
        </p>

        <table id="example" class="display table-striped" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>{{ trans('backoffice.label') }}</th>
                    <th>{{ trans('backoffice.route') }}</th>
                    <th class="col-md-2">{{ trans('backoffice.options') }}</th>
                </tr>
            </thead>

            <tfoot>
                <tr>
                    <th>{{ trans('backoffice.label') }}</th>
                    <th>{{ trans('backoffice.route') }}</th>
                    <th>{{ trans('backoffice.options') }}</th>
                </tr>
            </tfoot>
        </table>

    </div>
</div>

@stop

@section('script')
<script src=" {{ asset('assets/js/jquery.dataTables.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#example').dataTable({
            "ajax": "{{ url('admin/permissions') }}",
            "columns": [
                { "data": "label" },
                {
                    "render": function ( data, type, full, meta ) {
                        return full.http + ' ' + full.route;
                    }
                },
                {
                    "orderable": false,
                    "searchable": false,
                    "render": function ( data, type, full, meta ) {
                        return '<a class="btn btn-info btn-xs" href="' + "{{ url('admin/permissions/form') }}/" + full.id + '"><i class="fa fa-pencil"></i></a>'
                            + '&nbsp;<a class="btn btn-danger btn-xs" href="' + "{{ url('admin/permissions/delete') }}/" + full.id + '"><i class="fa fa-trash"></i></a>'
                    }
                }
            ]
        });
    });
</script>
@stop
