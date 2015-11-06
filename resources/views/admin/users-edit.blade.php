<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_user') }}</h3>
        
        <form id="formUser" method="POST" action="{{ url('/admin/users') }}">
            
            {!! csrf_field() !!}
            
            <p class="text-success v-success"></p>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{{ trans('backoffice.general') }}</a></li>
                <li role="presentation"><a href="#social" aria-controls="social" role="tab" data-toggle="tab">{{ trans('backoffice.social_ids') }}</a></li>
                <li role="presentation"><a href="#roles" aria-controls="roles" role="tab" data-toggle="tab">{{ trans('backoffice.roles') }}</a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane fade in active" id="general">
                    
                    <input type="hidden" name="id" value="{{ $user->id }}" />

                    <div class="form-group">
                        <label for="registerName">{{ trans('backoffice.name') }}</label>
                        <input class="form-control" type="text" name="name" id="registerName" value="{{ $user->name }}">
                        <span class="help-block alert-danger v-error-name"></span>
                    </div>

                    <div class="form-group">
                        <label for="registerEmail">{{ trans('backoffice.email') }}</label>
                        <input class="form-control" type="email" name="email" id="registerEmail" value="{{ $user->email }}">
                        <span class="help-block alert-danger v-error-email"></span>
                    </div>

                    <div class="form-group">
                        <label for="registerPassword">{{ trans('backoffice.password') }}</label>
                        <input class="form-control" type="password" name="password" id="registerPassword">
                        <span class="help-block alert-danger v-error-password"></span>
                    </div>

                    <div class="form-group">
                        <label for="registerPassword2">{{ trans('backoffice.confirm_password') }}</label>
                        <input class="form-control" type="password" name="password_confirmation"  id="registerPassword2">
                    </div>
                    
                </div>
                <div role="tabpanel" class="tab-pane fade" id="social">
                    
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
                <div role="tabpanel" class="tab-pane fade" id="roles">
                    
                    @foreach(App\Role::all() as $item)
                    <div class="checkbox">
                        <label>
                            <input name="roles[]" type="checkbox" value="{{ $item->id }}" 
                                @if($user->hasRole($item)) checked="checked" @endif>
                            {{ $item->name }}
                        </label>
                    </div>
                    @endforeach
                    
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
    var form = new Form($, '#formUser');
</script>
@stop