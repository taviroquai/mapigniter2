<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_page') }}</h3>
        
        <form id="formPage" method="POST" action="{{ url('/admin/pages') }}">
            
            {!! csrf_field() !!}
            
            <p class="text-success v-success"></p>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{{ trans('backoffice.general') }}</a></li>
                <li role="presentation"><a href="#data" aria-controls="data" role="tab" data-toggle="tab">{{ trans('backoffice.data_php') }}</a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="general">
            
                    <input type="hidden" name="id" value="{{ $page->id }}" />

                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="name">{{ trans('backoffice.name') }}</label>
                                <input class="form-control" type="text" 
                                       @if($page->id) readonly @endif
                                       name="name" id="name" value="{{ $page->name }}">
                                <span class="help-block alert-danger v-error-name"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>{{ trans('backoffice.active') }}</label>
                                <div class="form-group">
                                    <label class="radio-inline" title="The page will be reachable">
                                        <input required type="radio" name="active" value="1" 
                                            @if($page->active) checked="checked" @endif> {{ trans('backoffice.yes') }}
                                    </label>
                                    <label class="radio-inline" title="The page will not be reachable">
                                        <input type="radio" name="active" value="0"
                                            @if(!$page->active) checked="checked" @endif> {{ trans('backoffice.no') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="route">{{ trans('backoffice.route') }}</label>
                        <input class="form-control" type="text" name="route" id="route" value="{{ $page->route }}">
                        <span class="help-block alert-danger v-error-route"></span>
                    </div>

                    <div class="form-group">
                        <label for="content">{{ trans('backoffice.view') }}</label>
                        <span class="help-block alert-danger v-error-content"></span>
                        <span class="help-block alert-danger v-error-permissions"></span>
                        <textarea class="form-control" type="text" name="content" rows="15">{{ $content }}</textarea>
                    </div>
                </div>
                
                <div role="tabpanel" class="tab-pane fade" id="data">
                    <div class="form-group">
                        <label for="data">{{ trans('backoffice.data_php') }}</label>
                        <span class="help-block alert-danger v-error-data"></span>
                        <span class="help-block alert-danger v-error-permissions"></span>
                        <textarea class="form-control" type="text" name="data" rows="15">{{ $data }}</textarea>
                    </div>
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
    var form = new Form($, '#formPage');
</script>
@stop