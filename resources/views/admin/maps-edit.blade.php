<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('style')
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_map') }}</h3>
        
        <form id="formMap" method="POST" action="{{ url('/admin/maps') }}" enctype="mutipart/form-data">
            
            {!! csrf_field() !!}
            
            <!-- Set this param to resize images on server when uploading to prevent display unedited huge files -->
            <input type="hidden" name="id" value="{{ $map->id }}" />
            
            <p class="text-success v-success"></p>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#geo" aria-controls="geo" role="tab" data-toggle="tab">{{ trans('backoffice.general') }}</a></li>
                @if(!empty($map->id))
                <li role="presentation"><a href="#layers" aria-controls="layers" role="tab" data-toggle="tab">{{ trans('backoffice.layers') }}</a></li>
                @endif
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                
                <div role="tabpanel" class="tab-pane fade in active" id="geo">
                    
                    @if(empty($map->id))
                    
                    <input type="hidden" name="role_permission" value="NONE" />
                    
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="title">{{ trans('backoffice.title') }}</label>
                                <input class="form-control" type="text" name="title" id="title" required
                                       value="">
                                <span class="help-block alert-danger v-error-title"></span>
                                <span class="help-block alert-danger v-error-seo_slug"></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="title">{{ trans('backoffice.idiom') }}</label>
                                <select class="form-control" name="lang">
                                    @foreach(\App\content::getAvailableLanguages() as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    @endif
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="srid">{{ trans('backoffice.projection') }}</label>
                                <select class="form-control" name="projection_id">
                                    @foreach(App\Projection::all() as $item)
                                    <option value="{{ $item->srid }}"
                                        @if($item->srid === $map->projection_id) selected @endif>{{ $item->srid }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>{{ trans('backoffice.map_center') }}</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="center[]" required
                                            placeholder="X"
                                            value="{{ $center[0] }}">
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-control" type="text" name="center[]" required
                                            placeholder="Y"
                                            value="{{ $center[1] }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="zoom">{{ trans('backoffice.map_zoom') }}</label>
                                <input class="form-control" type="text" name="zoom" id="zoom" required
                                       placeholder="Zoom level"
                                       value="{{ $map->zoom }}">
                                <span class="help-block alert-danger v-error-zoom"></span>
                            </div>
                        </div>
                    </div>
                    
                </div>
                
                @if(!empty($map->id))
                <div role="tabpanel" class="tab-pane fade in" id="layers">
                    
                    <div class="row">
                        <div class="col-md-12">
                            
                            <table class="table table-striped" id="tableLayeritem">
                                <thead>
                                    <tr>
                                        <th>{{ trans('backoffice.title') }}</th>
                                        <th class="col-md-2">
                                            <button class="btn btn-primary btn-xs action-add-layer">Add Layer</button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(App\Layeritem::where('map_id', $map->id)->orderBy('displayorder')->get() as $item)
                                    <tr>
                                        <td>{{ $item->layer->content->title }}</td>
                                        <td>
                                            <a class="btn btn-danger btn-xs action-layeritem-del" href="{{ url('admin/maps/'.$map->id.'/layeritem/del/' . $item->id) }}">
                                                <i class="fa fa-trash"></i>
                                            </a>
                                            <a class="btn btn-default btn-xs action-layeritem-orderup" href="{{ url('admin/maps/'.$map->id.'/layeritem/orderup/' . $item->id) }}">
                                                <i class="fa fa-arrow-up"></i>
                                            </a>
                                            <a class="btn btn-default btn-xs action-layeritem-orderdown" href="{{ url('admin/maps/'.$map->id.'/layeritem/orderdown/' . $item->id) }}">
                                                <i class="fa fa-arrow-down"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                </div>
                @endif
                
            </div>

            <div class="form-group">
                <input class="btn btn-primary" type="submit" name="close" value="{{ trans('backoffice.saveclose') }}" />
                <button class="btn btn-success" type="submit" >{{ trans('backoffice.save') }}</button>
                <a href="javascript: window.history.back()" class="btn btn-danger">{{ trans('backoffice.cancel') }}</a>
            </div>
        </form>

    </div>
</div>

@stop

@section('script')
<div class="modal fade" id="modal">
    <div class="modal-dialog">
        <div class="modal-content">
            
            <form id="formLayeritem" method="POST" action="{{ url('/admin/maps/layeritem/add') }}" enctype="mutipart/form-data">
            
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Layer</h4>
                </div>
                <div class="modal-body">

                    {!! csrf_field() !!}
                    
                    <input type="hidden" name="map_id" value="{{ $map->id }}" />
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="layer_id">{{ trans('backoffice.layer') }}</label>
                                <select class="form-control" name="layer_id">
                                    @foreach(App\Layer::all() as $item)
                                    <option value="{{ $item->id }}">{{ $item->content->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="parent_id">{{ trans('backoffice.layergroup') }}</label>
                                <select class="form-control" name="parent_id">
                                    <option value="0">None</option>
                                    @foreach(App\Layer::where('type', 'group')->get() as $item)
                                    <option value="{{ $item->id }}">{{ $item->content->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ trans('backoffice.baselayer') }}</label>
                                <div class="form-group">
                                    <label class="radio-inline" title="The layer will be exclusive">
                                        <input required type="radio" name="baselayer" value="1"> {{ trans('backoffice.yes') }}
                                    </label>
                                    <label class="radio-inline" title="The layer will not be exclusive">
                                        <input type="radio" name="baselayer" value="0" checked> {{ trans('backoffice.no') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>{{ trans('backoffice.visible') }}</label>
                                <div class="form-group">
                                    <label class="radio-inline" title="The layer will be visible by default">
                                        <input required type="radio" name="visible" value="1" checked> {{ trans('backoffice.yes') }}
                                    </label>
                                    <label class="radio-inline" title="The layer will not be visible by default">
                                        <input type="radio" name="visible" value="0"> {{ trans('backoffice.no') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                
            </form>
                
        </div>
    </div>
</div>
<script type="text/html" id="layeritem_tpl">
    <tr>
        <td>@{{ title }}</td>
        <td>
            <a class="btn btn-danger btn-xs action-layeritem-del" href="@{{ urldel }}">
                <i class="fa fa-trash"></i>
            </a>
            <a class="btn btn-default btn-xs action-layeritem-orderup" href="@{{ urlorderup }}">
                <i class="fa fa-arrow-up"></i>
            </a>
            <a class="btn btn-default btn-xs action-layeritem-orderdown" href="@{{ urlorderdown }}">
                <i class="fa fa-arrow-down"></i>
            </a>
        </td>
    </tr>
</script>
<script src="{{ asset('assets/js/mustache.min.js') }}"></script>
<script type="text/javascript">
    
    $('#tableLayeritem .action-add-layer').on('click', function (e) {
        e.preventDefault();
        $('#modal').modal('show');
    });
    
    $('#tableLayeritem').on('click', '.action-layeritem-del', function (e) {
        e.preventDefault();
        var me = $(this);
        $.getJSON(me.attr('href'), function (r) {
            if (r.success) {
                me.closest('tr').first().remove();
            }
        });
        return false;
    });
    
    $('#tableLayeritem').on('click', '.action-layeritem-orderup', function (e) {
        e.preventDefault();
        var me = $(this);
        $.getJSON(me.attr('href'), function (r) {
            if (r.success) {
                me.closest('tr').insertBefore(me.closest('tr').prev());
            }
        });
        return false;
    });
    
    $('#tableLayeritem').on('click', '.action-layeritem-orderdown', function (e) {
        e.preventDefault();
        var me = $(this);
        $.getJSON(me.attr('href'), function (r) {
            if (r.success) {
                me.closest('tr').insertAfter(me.closest('tr').next());
            }
        });
        return false;
    });
    
    var formMap = new Form($, '#formMap');
    var formLayeritem = new Form($, '#formLayeritem', {
        cb: function (r) {
            if (r.success) {
                $('#modal').modal('hide');
                var data = {
                    urldel: "{{ url ('admin/maps/' . $map->id . '/layeritem/del') }}/" + r.id,
                    urlorderup: "{{ url ('admin/maps/' . $map->id . '/layeritem/orderup') }}/" + r.id,
                    urlorderdown: "{{ url ('admin/maps/' . $map->id . '/layeritem/orderdown') }}/" + r.id,
                    title: r.title
                }
                $('#tableLayeritem tbody').append(Mustache.render($('#layeritem_tpl').html(), data));
            }
        }
    });
    
</script>
@stop