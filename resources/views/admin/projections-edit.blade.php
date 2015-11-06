<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('style')
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_projection') }}</h3>
        
        <form id="formProjection" method="POST" action="{{ url('/admin/projections') }}" enctype="mutipart/form-data">
            
            {!! csrf_field() !!}
            
            <input type="hidden" name="id" value="{{ $projection->id }}" />
            
            <p class="text-success v-success"></p>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">{{ trans('backoffice.general') }}</a></li>
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                
                <div role="tabpanel" class="tab-pane fade in active" id="general">
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="srid">{{ trans('backoffice.srid') }}</label>
                                <select class="form-control" name="srid">
                                    @foreach(App\Projection::sridOptions() as $item)
                                    <option value="{{ $item->srid }}"
                                        @if($item->srid === $projection->srid) selected @endif>{{ $item->srid }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="proj4_params">{{ trans('backoffice.proj4_params') }}</label>
                                <span class="help-block alert-danger v-error-proj4_params"></span>
                                <textarea class="form-control" type="text" 
                                    rows="5"
                                    name="proj4_params" id="proj4_params"
                                    placeholder="">{{ $projection->proj4_params }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ trans('backoffice.extent') }}</label>
                                <div class="row">
                                    <div class="col-md-3">
                                        <input class="form-control" type="text" name="extent[]" required
                                            placeholder="Min x"
                                            value="{{ $extent[0] }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control" type="text" name="extent[]" required
                                            placeholder="Min y"
                                            value="{{ $extent[1] }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control" type="text" name="extent[]" required
                                            placeholder="Max x"
                                            value="{{ $extent[2] }}">
                                    </div>
                                    <div class="col-md-3">
                                        <input class="form-control" type="text" name="extent[]" required
                                            placeholder="Max y"
                                            value="{{ $extent[3] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
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
<script type="text/javascript">
    
    var formMap = new Form($, '#formProjection');
    
</script>
@stop