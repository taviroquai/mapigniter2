<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('style')
<link href="{{ asset('assets/css/fileinput.min.css') }}" rel="stylesheet">
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_brand') }}</h3>
        
        <form id="formContent" method="POST" action="{{ url('/admin/brands') }}" enctype="mutipart/form-data">
            
            {!! csrf_field() !!}
            
            <p class="text-success v-success"></p>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{{ trans('backoffice.general') }}</a></li>
                <li role="presentation"><a href="#style" aria-controls="style" role="tab" data-toggle="tab">{{ trans('backoffice.style') }}</a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="general">

                    <input type="hidden" name="id" value="{{ $brand->id }}" />
                    
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="name">{{ trans('backoffice.name') }}</label>
                                <input class="form-control" type="text" name="name" id="name" value="{{ $brand->name }}">
                                <span class="help-block alert-danger v-error-name"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="name">{{ trans('backoffice.active') }}</label>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="active" value="1"
                                       @if($brand->active) checked @endif> {{ trans('backoffice.active') }}
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slogan">{{ trans('backoffice.slogan') }}</label>
                        <input class="form-control" type="text" name="slogan" id="slogan" value="{{ $brand->slogan }}">
                        <span class="help-block alert-danger v-error-slogan"></span>
                    </div>

                    <div class="form-group">
                        <label for="description">{{ trans('backoffice.description') }}</label>
                        <input class="form-control" type="text" name="description" id="description" value="{{ $brand->description }}">
                        <span class="help-block alert-danger v-error-description"></span>
                    </div>

                    <div class="form-group">
                        <label for="keywords">{{ trans('backoffice.keywords') }}</label>
                        <input class="form-control" type="text" name="keywords" id="keywords" value="{{ $brand->keywords }}">
                        <span class="help-block alert-danger v-error-keywords"></span>
                    </div>

                    <div class="form-group">
                        <label for="author">{{ trans('backoffice.author') }}</label>
                        <input class="form-control" type="text" name="author" id="author" value="{{ $brand->author }}">
                        <span class="help-block alert-danger v-error-author"></span>
                    </div>
                </div>
                
                <div role="tabpanel" class="tab-pane fade" id="style">
                    <div class="form-group">
                        <label for="logo">{{ trans('backoffice.logo') }}</label>
                        <input class="form-control" type="file" name="logo" id="logo" value="">
                        <span class="help-block alert-danger v-error-logo"></span>
                    </div>
                    <div class="form-group">
                        <label for="content">{{ trans('backoffice.css') }}</label>
                        <span class="help-block alert-danger v-error-css"></span>
                        <textarea class="form-control" type="text" name="css" rows="15">{{ $brand->css }}</textarea>
                    </div>
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
<script src=" {{ asset('assets/js/fileinput.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $("#logo").fileinput({
        @if ($brand->hasPicture())
        initialPreview: [
            "<img src=\"{{ $brand->getPictureUrl() }}\" class=\"file-preview-image\" alt=\"{{ $brand->name }}\" title=\"{{ $brand->name }}\">"
        ],
        @endif
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
		maxFileCount: 1,
    });
    
    var form = new Form($, '#formContent', {files: ['#logo']});
    
</script>
@stop