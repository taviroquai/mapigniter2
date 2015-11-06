<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_role') }}</h3>
        
        <form id="formRole" method="POST" action="{{ url('/admin/roles') }}">
            
            {!! csrf_field() !!}
            
            <p class="text-success v-success"></p>
            
            <input type="hidden" name="id" value="{{ $role->id }}" />

            <div class="form-group">
                <label for="name">{{ trans('backoffice.name') }}</label>
                <input class="form-control" type="text" name="name" id="name" value="{{ $role->name }}">
                <span class="help-block alert-danger v-error-name"></span>
            </div>
            
            <h4>{{ trans('backoffice.content_permissions') }}</h4>
            <div class="form-group">
                <label class="radio-inline" title="Can edit any content">
                    <input required type="radio" name="content_permission" value="NONE" 
                        @if($role->isContentPermission('NONE')) checked="checked" @endif> {{ trans('backoffice.permission_none') }}
                </label>
                <label class="radio-inline" title="Can edit content of other users in the same roles">
                    <input type="radio" name="content_permission" value="ROLE"
                        @if($role->isContentPermission('ROLE')) checked="checked" @endif> {{ trans('backoffice.permission_role') }}
                </label>
                <label class="radio-inline" title="Only can edit its own content">
                    <input type="radio" name="content_permission" value="USER"
                        @if($role->isContentPermission('USER')) checked="checked" @endif> {{ trans('backoffice.permission_user') }}
                </label>
            </div>
            
            <h4>{{ trans('backoffice.application_permissions') }}</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>{{ trans('backoffice.permission') }}</th>
                        <th class="col-md-2">{{ trans('backoffice.access') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(App\Permission::all() as $item)
                    <tr>
                        <td>{{ $item->label }}</td>
                        <td>
                            <label class="radio-inline">
                                <input required type="radio" name="permissions[{{ $item->id }}]['access']" value="deny" 
                                    @if(!$role->isPermissionAllow($item)) checked="checked" @endif> {{ trans('backoffice.deny') }}
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="permissions[{{ $item->id }}]['access']" value="allow"
                                    @if($role->isPermissionAllow($item)) checked="checked" @endif> {{ trans('backoffice.allow') }}
                            </label>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            

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
    var form = new Form($, '#formRole');
</script>
@stop