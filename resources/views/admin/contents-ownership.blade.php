
@extends('admin.layout')

@section('style')
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.ownership') }}</h3>
        
        <form id="formContent" method="POST" action="{{ url('/admin/contents/ownership/' . $content->id) }}">
            
            {!! csrf_field() !!}
            
            <p class="text-success v-success"></p>

            <div class="form-group">
                <label for="user_id">{{ trans('backoffice.user') }}</label>
                <select class="form-control" type="text" name="user_id" id="user_id">
                    @foreach( App\User::all() as $item)
                    <option value="{{ $item->id }}" @if ($item->id === $content->user_id) selected @endif>{{ $item->name }}</option>
                    @endforeach
                </select>
                <span class="help-block alert-danger v-error-user"></span>
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
    
    var form = new Form($, '#formContent');
    
</script>
@stop