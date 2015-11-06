<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('style')
<link href="{{ asset('assets/css/fileinput.min.css') }}" rel="stylesheet">
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.my_account') }}</h3>

        <form method="POST" action="{{ url('/admin/profile') }}" enctype="multipart/form-data">
            {!! csrf_field() !!}
            
            <!-- Set this param to resize images on server when uploading to prevent display unedited huge files -->
            <input type="hidden" name="image_max_width" value="1024" />

            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="registerName">{{ trans('backoffice.name') }}</label>
                        <input class="form-control" type="text" name="name" id="registerName" value="{{ old('name', $user->name) }}">
                        <span class="help-block alert-danger">{{ $errors->first('name') }}</span>
                    </div>

                    <div class="form-group">
                        <label for="registerEmail">{{ trans('backoffice.email') }}</label>
                        <input class="form-control" type="email" name="email" id="registerEmail" value="{{ old('email', $user->email) }}">
                        <span class="help-block alert-danger">{{ $errors->first('email') }}</span>
                    </div>

                    <div class="form-group">
                        <label for="registerPassword">{{ trans('backoffice.password') }}</label>
                        <input class="form-control" type="password" name="password" id="registerPassword">
                        <span class="help-block alert-danger">{{ $errors->first('password') }}</span>
                    </div>

                    <div class="form-group">
                        <label for="registerPassword2">{{ trans('backoffice.confirm_password') }}</label>
                        <input class="form-control" type="password" name="password_confirmation"  id="registerPassword2">
                    </div>

                    <h4>{{ trans('backoffice.social_ids') }}</h4>
                    <div class="form-group">
                        <label for="twitter_id">{{ trans('backoffice.twitter') }}</label>
                        <input class="form-control" type="text" name="twitter_id" id="twitter_id" value="{{ old('twitter_id', $user->twitter_id) }}">
                        <span class="help-block alert-danger v-error-twitter_id"></span>
                    </div>
                    <div class="form-group">
                        <label for="facebook_id">{{ trans('backoffice.facebook') }}</label>
                        <input class="form-control" type="text" name="facebook_id" id="facebook_id" value="{{ old('facebook_id', $user->facebook_id) }}">
                        <span class="help-block alert-danger v-error-facebook_id"></span>
                    </div>
                    <div class="form-group">
                        <label for="gplus_id">{{ trans('backoffice.gplus') }}</label>
                        <input class="form-control" type="text" name="gplus_id" id="gplus_id" value="{{ old('gplus_id', $user->gplus_id) }}">
                        <span class="help-block alert-danger v-error-gplus_id"></span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="avatar">{{ trans('backoffice.avatar') }}</label>
                        <input class="form-control" type="file" name="avatar" id="avatar" value="">
                        <span class="help-block alert-danger v-error-avatar"></span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-primary" type="submit">{{ trans('backoffice.save') }}</button>
            </div>
        </form>

    </div>
</div>

@stop

@section('script')
<script src="{{ asset('assets/js/fileinput.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    $("#avatar").fileinput({
        @if ($user->hasAvatar())
        initialPreview: [
            "<img src=\"{{ $user->getAvatarUrl() }}\" class=\"file-preview-image\" alt=\"{{ $user->name }}\" title=\"{{ $user->name }}\">"
        ],
        @endif
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
		maxFileCount: 1,
    });
</script>
@stop