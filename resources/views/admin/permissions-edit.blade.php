<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_permission') }}</h3>
        
        <form id="formPermission" method="POST" action="{{ url('/admin/permissions') }}">
            
            {!! csrf_field() !!}
            
            <p class="text-success v-success"></p>
            
            <input type="hidden" name="id" value="{{ $permission->id }}" />

            <div class="form-group">
                <label for="label">{{ trans('backoffice.label') }}</label>
                <input class="form-control" type="text" id="label" required
                       name="label" value="{{ $permission->label }}">
                <span class="help-block alert-danger v-error-label"></span>
            </div>
            
            <div class="form-group">
                <label for="label">{{ trans('backoffice.route') }}</label>
                <input class="form-control" type="text" id="route" required
                       name="route" value="{{ $permission->route }}">
                <span class="help-block alert-danger v-error-route"></span>
            </div>
            
            <div class="form-group">
                <label class="radio-inline">
                    <input required type="radio" name="http" value="get" checked> {{ trans('backoffice.http_get') }}
                </label>
                <label class="radio-inline">
                    <input type="radio" name="http" value="post"> {{ trans('backoffice.http_post') }}
                </label>
                <label class="radio-inline">
                    <input type="radio" name="http" value="put"> {{ trans('backoffice.http_put') }}
                </label>
                <label class="radio-inline">
                    <input type="radio" name="http" value="delete"> {{ trans('backoffice.http_delete') }}
                </label>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ trans('backoffice.save') }}</button>
                <a href="javascript: window.history.back()" class="btn btn-danger">{{ trans('backoffice.cancel') }}</a>
            </div>
        </form>

    </div>
</div>

@stop

@section('script')
<script type="text/javascript">
    var form = new Form($, '#formPermission');
</script>
@stop