<!-- resources/views/auth/register.blade.php -->

@extends('admin.layout')

@section('style')
<link href="{{ asset('assets/css/fileinput.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/css/ol.css') }}" rel="stylesheet">
<style type="text/css">
    #map {
        width: 100%;
        height: 480px;
    }
</style>
@stop

@section('content')

<div class="row">
    <div class="col-md-12">
                
        <h3>{{ trans('backoffice.edit_layer') }}</h3>
        
        <form id="formLayer" method="POST" action="{{ url('/admin/layers') }}" enctype="mutipart/form-data">
            
            {!! csrf_field() !!}
            
            <input type="hidden" name="id" value="{{ $layer->id }}" />
            
            <p class="text-success v-success"></p>
            
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#geo" aria-controls="geo" role="tab" data-toggle="tab">{{ trans('backoffice.general') }}</a></li>
                <li role="presentation"><a href="#icons" aria-controls="icons" role="tab" data-toggle="tab">{{ trans('backoffice.icons') }}</a></li>
                @if($layer->type === 'geojson')
                <li role="presentation"><a href="#features" aria-controls="features" role="tab" data-toggle="tab">{{ trans('backoffice.layer_mapeditor') }}</a></li>
                <li role="presentation"><a href="#import" aria-controls="import" role="tab" data-toggle="tab">{{ trans('backoffice.layer_import') }}</a></li>
                @endif
            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content">
                
                <div role="tabpanel" class="tab-pane fade in active" id="geo">
                    
                    @if(empty($layer->id))
                    
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
                                <label for="type">{{ trans('backoffice.layer_type') }}</label>
                                <select class="form-control" name="type">
                                    @foreach(App\Layer::typeOptions() as $k => $label)
                                    <option value="{{ $k }}"
                                        @if($k === $layer->type) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <span class="vector-warning text-danger">{{ trans('backoffice.vector_warning') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="projection_options" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="srid">{{ trans('backoffice.projection') }}</label>
                                <select class="form-control" name="projection_id">
                                    @foreach(App\Projection::all() as $item)
                                    <option value="{{ $item->srid }}"
                                        @if($item->srid === $layer->projection_id) selected @endif>{{ $item->srid }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="bing_options" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bing_key">{{ trans('backoffice.bing_key') }}</label>
                                <input class="form-control" type="text" name="bing_key"
                                    placeholder=""
                                    value="{{ $layer->bing_key }}">
                                <span class="help-block alert-danger v-error-bing_key"></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bing_imageryset">{{ trans('backoffice.bing_imageryset') }}</label>
                                <select class="form-control" name="bing_imageryset" id="bing_imageryset">
                                    @foreach(App\Layer::bingImageryOptions() as $item)
                                    <option value="{{ $item }}"
                                        @if($item === $layer->bing_imageryset) selected @endif>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row" id="mapquest_options" style="display: none;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mapquest_layer">{{ trans('backoffice.mapquest_layer') }}</label>
                                <select class="form-control" name="mapquest_layer" id="mapquest_layer">
                                    @foreach(App\Layer::mapquestLayerOptions() as $k => $label)
                                    <option value="{{ $k }}"
                                        @if($k === $layer->bing_imageryset) selected @endif>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div id="wms_options" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="wms_servertype">{{ trans('backoffice.wms_servertype') }}</label>
                                    <select class="form-control" name="wms_servertype" id="wms_servertype">
                                        @foreach(App\Layer::wmsServerTypeOptions() as $k => $label)
                                        <option value="{{ $k }}"
                                            @if($k === $layer->wms_servertype) selected @endif>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="wms_url">{{ trans('backoffice.wms_url') }}</label>
                                    <input class="form-control" type="text" name="wms_url"
                                        placeholder=""
                                        value="{{ $layer->wms_url }}">
                                    <span class="help-block alert-danger v-error-wms_url"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="wms_version">{{ trans('backoffice.wms_version') }}</label>
                                    <select class="form-control" name="wms_version" id="wms_version">
                                        @foreach(App\Layer::wmsVersionOptions() as $k => $label)
                                        <option value="{{ $k }}"
                                            @if($k === $layer->wms_version) selected @endif>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="wms_tiled">{{ trans('backoffice.wms_tiled') }}</label>
                                    <select class="form-control" name="wms_tiled" id="wms_tiled">
                                        @foreach(App\Layer::wmsTiledOptions() as $k => $label)
                                        <option value="{{ $k }}"
                                            @if($k === $layer->wms_tiled) selected @endif>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Load</label>
                                    <div class="clearfix"></div>
                                    <button class="btn btn-info getwmscapabilities">Get Capabilities</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="wms_layers">{{ trans('backoffice.wms_layers') }}</label>
                                    <select class="form-control" name="wms_layers[]" multiple="multiple">
                                        @foreach(explode(',', $layer->wms_layers) as $option)
                                        <option value="{{ $option }}" selected>{{ $option }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block alert-danger v-error-wms_layers"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div id="wfs_options" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="wfs_url">{{ trans('backoffice.wfs_url') }}</label>
                                    <input class="form-control" type="text" name="wfs_url"
                                        placeholder=""
                                        value="{{ $layer->wfs_url }}">
                                    <span class="help-block alert-danger v-error-wfs_url"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="wfs_version">{{ trans('backoffice.wfs_version') }}</label>
                                    <select class="form-control" name="wfs_version" id="wfs_version">
                                        @foreach(App\Layer::wfsVersionOptions() as $k => $label)
                                        <option value="{{ $k }}"
                                            @if($k === $layer->wfs_version) selected @endif>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Load</label>
                                    <div class="clearfix"></div>
                                    <button class="btn btn-info getwfscapabilities">Get Capabilities</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="wfs_typename">{{ trans('backoffice.wfs_typename') }}</label>
                                    <select class="form-control" name="wfs_typename">
                                        <option value="{{ $layer->wfs_typename }}" selected>{{ $layer->wfs_typename }}</option>
                                    </select>
                                    <span class="help-block alert-danger v-error-wfs_typename"></span>
                                    <span class="help-block wfs-attributes"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="gpx_options" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="gpx_filename">{{ trans('backoffice.gpx_filename') }}</label>
                                @if ($layer->gpx_filename)
                                <span><small>Previous: {{ $layer->gpx_filename }}</small></span>
                                @endif
                                <input class="form-control" type="file" name="gpx_filename" id="gpx_filename" value="">
                                <span class="help-block alert-danger v-error-gpx_filename_0"></span>
                                <span class="help-block gpx-attributes"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="kml_options" style="display: none;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="kml_filename">{{ trans('backoffice.kml_filename') }}</label>
                                @if ($layer->kml_filename)
                                <span><small>Previous: {{ $layer->kml_filename }}</small></span>
                                @endif
                                <input class="form-control" type="file" name="kml_filename" id="kml_filename" value="">
                                <span class="help-block alert-danger v-error-kml_filename_0"></span>
                                <span class="help-block kml-attributes"></span>
                            </div>
                        </div>
                    </div>
                    <div id="shapefile_options" style="display: none;">
                        @if ($layer->shapefile_wmsurl)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-success" role="alert">
                                    {{ trans('backoffice.shareogcservice') }} <a href="{{ $layer->shapefile_wmsurl }}&SERVICE=WMS&VERSION=1.1.0&REQUEST=GetCapabilities" target="_blank">
                                        {{ $layer->shapefile_wmsurl }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shapefile_filename">{{ trans('backoffice.shapefile_filename') }}</label>
                                    @if ($layer->shapefile_filename)
                                    <span><small>Previous: {{ $layer->shapefile_filename }}</small></span>
                                    @endif
                                    <input class="form-control" type="file" name="shapefile_filename" id="shapefile_filename" value="">
                                    <span class="help-block alert-danger v-error-shapefile_filename_0"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="shapefile_geomtype">{{ trans('backoffice.shapefile_geomtype') }}</label>
                                    <select class="form-control" name="shapefile_geomtype">
                                        <option value="POINT" @if($layer->shapefile_geomtype === 'POINT') selected @endif>POINT</option>
                                        <option value="LINE" @if($layer->shapefile_geomtype === 'LINE') selected @endif>LINE</option>
                                        <option value="POLYGON" @if($layer->shapefile_geomtype === 'POLYGON') selected @endif>POLYGON</option>
                                    </select>
                                    <span class="help-block alert-danger v-error-shapefile_geomtype"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="shapefile_msclass">{{ trans('backoffice.shapefile_msclass') }}
                                        <small><a href="http://mapserver.org/documentation.html" target="_blank">{{ trans('backoffice.mapserver_link') }}</a></small>
                                    </label>
                                    <textarea rows="15" class="form-control"
                                        name="shapefile_msclass">{{ $layer->shapefile_msclass }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="postgis_options" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <span class="help-block alert-danger v-error-postgis_error"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_host">{{ trans('backoffice.postgis_host') }}</label>
                                    <input class="form-control" type="text" name="postgis_host"
                                        placeholder=""
                                        value="{{ $layer->postgis_host }}">
                                    <span class="help-block alert-danger v-error-postgis_host"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_port">{{ trans('backoffice.postgis_port') }}</label>
                                    <input class="form-control" type="text" name="postgis_port"
                                        placeholder=""
                                        value="{{ empty($layer->postgis_port) ? 5432 : $layer->postgis_port }}">
                                    <span class="help-block alert-danger v-error-postgis_port"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_dbname">{{ trans('backoffice.postgis_dbname') }}</label>
                                    <input class="form-control" type="text" name="postgis_dbname"
                                        placeholder=""
                                        value="{{ $layer->postgis_dbname }}">
                                    <span class="help-block alert-danger v-error-postgis_dbname"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_user">{{ trans('backoffice.postgis_user') }}</label>
                                    <input class="form-control" type="text" name="postgis_user"
                                        placeholder=""
                                        value="{{ $layer->postgis_user }}">
                                    <span class="help-block alert-danger v-error-postgis_user"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_pass">{{ trans('backoffice.postgis_pass') }}</label>
                                    <input class="form-control" type="password" name="postgis_pass"
                                        placeholder=""
                                        value="">
                                    <span class="help-block alert-danger v-error-postgis_pass"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>{{ trans('backoffice.postgis_connect') }}</label>
                                    <div class="clearfix"></div>
                                    <button class="btn btn-info getpostgisschemas">{{ trans('backoffice.postgis_connect') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_schema">{{ trans('backoffice.postgis_schema') }}</label>
                                    <select class="form-control" name="postgis_schema">
                                        @if ($layer->postgis_schema)
                                        <option value="{{ $layer->postgis_schema }}">{{ $layer->postgis_schema }}</option>
                                        @endif
                                    </select>
                                    <span class="help-block alert-danger v-error-postgis_schema"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_table">{{ trans('backoffice.postgis_table') }}</label>
                                    <select class="form-control" name="postgis_table">
                                        @if ($layer->postgis_table)
                                        <option value="{{ $layer->postgis_table }}">{{ $layer->postgis_table }}</option>
                                        @endif
                                    </select>
                                    <span class="help-block alert-danger v-error-postgis_table"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="postgis_field">{{ trans('backoffice.postgis_field') }}</label>
                                    <select class="form-control" name="postgis_field">
                                        @if ($layer->postgis_field)
                                        <option value="{{ $layer->postgis_field }}">{{ $layer->postgis_field }}</option>
                                        @endif
                                    </select>
                                    <span class="help-block alert-danger v-error-postgis_field"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="postgis_attributes">{{ trans('backoffice.postgis_attributes') }}</label>
                                    <select class="form-control" 
                                        name="postgis_attributes[]"
                                        multiple="multiple">
                                        @if ($layer->postgis_attributes)
                                        @foreach(explode(',', $layer->postgis_attributes) as $attr)
                                        <option selected value="{{ $attr }}">{{ $attr }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span class="help-block alert-danger v-error-postgis_attributes"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="geojson_options" style="display: none;">
                        @if ($layer->id)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="alert alert-success" role="alert">
                                    {{ trans('backoffice.sharegeojson') }} <a href="{{ url('storage/layer/'.$layer->id.'/geojson.json') }}"  target="_blank">
                                        {{ url('storage/layer/'.$layer->id.'/geojson.json') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="geojson_geomtype">{{ trans('backoffice.geojson_geomtype') }}</label>
                                    <select class="form-control" name="geojson_geomtype">
                                        <option value="Point" @if($layer->geojson_geomtype === 'Point') selected @endif>Point</option>
                                        <option value="LineString" @if($layer->geojson_geomtype === 'LineString') selected @endif>LineString</option>
                                        <option value="Polygon" @if($layer->geojson_geomtype === 'Polygon') selected @endif>Polygon</option>
                                    </select>
                                    <span class="help-block alert-danger v-error-geojson_geomtype"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="geojson_attributes">{{ trans('backoffice.geojson_attributes') }}</label>
                                    <input class="form-control" type="text" name="geojson_attributes"
                                        placeholder=""
                                        value="{{ $layer->geojson_attributes }}">
                                    <span class="help-block alert-danger v-error-geojson_attributes"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <textarea rows="5" class="form-control" style="display: none"
                                        name="geojson_features">{{ $layer->geojson_features }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="geopackage_options" style="display: none;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="geopackage_filename">{{ trans('backoffice.geopackage_filename') }}</label>
                                @if ($layer->geopackage_filename)
                                <span><small>Previous: {{ $layer->geopackage_filename }}</small></span>
                                @endif
                                <input class="form-control" type="file" name="geopackage_filename" id="geopackage_filename" value="">
                                <span class="help-block alert-danger v-error-geopackage_filename_0"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="geopackage_table">{{ trans('backoffice.geopackage_table') }}</label>
                                <select class="form-control" name="geopackage_table">
                                    <option value="{{ $layer->geopackage_table }}" selected>{{ $layer->geopackage_table }}</option>
                                </select>
                                <span class="help-block alert-danger v-error-geopackage_table"></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="geopackage_fields">{{ trans('backoffice.geopackage_fields') }}</label>
                                <select class="form-control" name="geopackage_fields[]" multiple="multiple">
                                    @foreach(explode(',', $layer->geopackage_fields) as $option)
                                    <option value="{{ $option }}" selected>{{ $option }}</option>
                                    @endforeach
                                </select>
                                <span class="help-block alert-danger v-error-geopackage_fields"></span>
                            </div>
                        </div>
                    </div>
                    <div id="vector_options" style="display: none;">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="feature_info_template">{{ trans('backoffice.feature_info_template') }} 
                                        <small><a href="https://docs.angularjs.org/tutorial/step_02" target="_blank">{{ trans('backoffice.angularjs_link') }}</a></small>
                                    </label>
                                    <span class="help-block alert-danger v-error-feature_info_template"></span>
                                    <textarea class="form-control" type="text" 
                                        rows="5"
                                        name="feature_info_template" id="feature_info_template"
                                        placeholder="">{{ $layer->feature_info_template }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="search">{{ trans('backoffice.layer_search') }}</label>
                                    <select class="form-control"
                                        name="search[]"
                                        id="search"
                                        multiple>
                                        @foreach(explode(',', $layer->search) as $attr)
                                        <option value="{{ $attr }}" selected>{{ $attr }}</option>
                                        @endforeach
                                    </select>
                                    <span class="help-block alert-danger"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <fieldset id="style_options" style="display: none;">
                        <legend>{{ trans('backoffice.static_style') }}</legend>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_static_icon">{{ trans('backoffice.ol_style_static_icon') }}</label>
                                    @if ($layer->ol_style_static_icon)
                                    <span><img style="max-height: 30px" src="{{ asset('storage/layer/'.$layer->id.'/'.$layer->ol_style_static_icon) }}" /></span>
                                    @endif
                                    <input class="form-control" type="file" name="ol_style_static_icon" id="ol_style_static_icon" value="">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_static_fill_color">{{ trans('backoffice.ol_style_static_fill_color') }}</label>
                                    <span class="help-block alert-danger v-error-ol_style_static_fill_color"></span>
                                    <div class="input-group">
                                        <input class="form-control" type="text" 
                                        name="ol_style_static_fill_color" id="ol_style_static_fill_color"
                                        placeholder="" value="{{ $layer->ol_style_static_fill_color }}" />
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_static_stroke_color">{{ trans('backoffice.ol_style_static_stroke_color') }}</label>
                                    <span class="help-block alert-danger v-error-ol_style_static_stroke_color"></span>
                                    <div class="input-group">
                                        <input class="form-control" type="text" 
                                            name="ol_style_static_stroke_color" id="ol_style_static_stroke_color"
                                            placeholder="" value="{{ $layer->ol_style_static_stroke_color }}" />
                                        <span class="input-group-addon"><i></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_static_stroke_width">{{ trans('backoffice.ol_style_static_stroke_width') }}</label>
                                    <span class="help-block alert-danger v-error-ol_style_static_stroke_width"></span>
                                    <input class="form-control" type="text" 
                                        name="ol_style_static_stroke_width" id="ol_style_static_stroke_width"
                                        placeholder="" value="{{ $layer->ol_style_static_stroke_width }}" />
                                </div>
                            </div>
                        </div>
                        <legend>{{ trans('backoffice.dynamic_style') }}</legend>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_field_icon">{{ trans('backoffice.ol_style_field_icon') }}</label>
                                    <select class="form-control"
                                        name="ol_style_field_icon"
                                        id="ol_style_field_icon">
                                        <option value="{{ $layer->ol_style_field_icon }}" selected>
                                            {{ $layer->ol_style_field_icon }}
                                        </option>
                                    </select>
                                    <span class="help-block alert-danger v-error-ol_style_field_icon"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_field_fill_color">{{ trans('backoffice.ol_style_field_fill_color') }}</label>
                                    <select class="form-control"
                                        name="ol_style_field_fill_color"
                                        id="ol_style_field_fill_color">
                                        <option value="{{ $layer->ol_style_field_fill_color }}" selected>
                                            {{ $layer->ol_style_field_fill_color }}
                                        </option>
                                    </select>
                                    <span class="help-block alert-danger v-error-ol_style_field_fill_color"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_field_stroke_color">{{ trans('backoffice.ol_style_field_stroke_color') }}</label>
                                    <select class="form-control"
                                        name="ol_style_field_stroke_color"
                                        id="ol_style_field_stroke_color">
                                        <option value="{{ $layer->ol_style_field_stroke_color }}" selected>
                                            {{ $layer->ol_style_field_stroke_color }}
                                        </option>
                                    </select>
                                    <span class="help-block alert-danger v-error-ol_style_field_stroke_color"></span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="ol_style_field_stroke_width">{{ trans('backoffice.ol_style_field_stroke_width') }}</label>
                                    <select class="form-control"
                                        name="ol_style_field_stroke_width"
                                        id="ol_style_field_stroke_width">
                                        <option value="{{ $layer->ol_style_field_stroke_width }}" selected>
                                            {{ $layer->ol_style_field_stroke_width }}
                                        </option>
                                    </select>
                                    <span class="help-block alert-danger v-error-ol_style_field_stroke_width"></span>
                                </div>
                            </div>
                        </div>
                    </fieldset>
                    
                </div>
                
                <div role="tabpanel" class="tab-pane fade" id="icons">
                    
                    <h4>{{ trans('backoffice.current_images') }}</h4>
                    <div class="row">
                        @foreach($layer->getIconsImages() as $item)
                        <div class="col-md-2">
                            <img class="col-md-12 thumbnail" src="{{ $layer->getIconImageUrl($item) }}" />
                            <a class="btn btn-danger delete-image" data-item="{{ $layer->getIconImageUrl($item) }}"
                                title="Click to delete">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="form-group">
                        <label for="image_uploader">{{ trans('backoffice.upload_images') }}</label>
                        <input class="form-control" type="file" name="image_uploader[]" id="image_uploader">
                    </div>
                    
                </div>
                
                @if($layer->type === 'geojson')
                <div role="tabpanel" class="tab-pane fade in active" id="features">
                    
                    <h4>{{ trans('backoffice.layer_mapeditor') }}</h4>
                    
                    <div class="btn-group" role="group" aria-label="toggleInteraction" id="toggleInteraction">
                        <button type="button" class="btn btn-default" id="interactionCreate">Create</button>
                        <button type="button" class="btn btn-default" id="interactionModify">Modify</button>
                        <button type="button" class="btn btn-default" id="interactionSelect">Edit Attributes</button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div id="map" class="map"></div>
                        </div>
                    </div>
                    
                </div>
                <div role="tabpanel" class="tab-pane fade" id="import">
                    
                    <h4>{{ trans('backoffice.layer_import') }}</h4>
                    
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div class="form-group">
                                <label for="csv_uploader">{{ trans('backoffice.upload_csv') }}</label>
                                <input class="form-control" type="file" name="csv_uploader" id="csv_uploader">
                            </div>

                        </div>
                    </div>
                    
                </div>
                @endif
                
            </div>

            <div class="form-group">
                <input class="btn btn-primary" type="submit" name="close" value="{{ trans('backoffice.saveclose') }}" />
                <button class="btn btn-success" type="submit">{{ trans('backoffice.save') }}</button>
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
            
            <form id="formFeature" method="POST" action="#">
            
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Feature</h4>
                </div>
                <div class="modal-body">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
                
            </form>
                
        </div>
    </div>
</div>
<script type="text/html" id="feature_attribute_tpl">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>@{{ name }}</label>
                <input type="text" class="form-control" name="@{{ name }}" value="@{{ value }}" />
            </div>
        </div>
    </div>
</script>
<script src="{{ asset('assets/js/mustache.min.js') }}"></script>
<script src="{{ asset('assets/js/fileinput.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/bootstrap-colorpicker.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/ol.js') }}" type="text/javascript"></script>
<script src="{{ asset('assets/js/OGCService.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    
    // tables options
    var tables = [];
    var attributes = [];
    
    // Update selectable attributes
    function updateSelectAttribute() {
        var search = [],
            ol_style_field_icon = $('[name="ol_style_field_icon"]').val(),
            ol_style_field_fill_color = $('[name="ol_style_field_fill_color"]').val(),
            ol_style_field_stroke_color = $('[name="ol_style_field_stroke_color"]').val(),
            ol_style_field_stroke_width = $('[name="ol_style_field_stroke_width"]').val();
        $('[name="search[]"] :selected').each(function(i, selected){ 
            search.push($(selected).text()); 
        });
    
        $('[name="search[]"]').empty();
        $('[name="ol_style_field_icon"]').empty();
        $('[name="ol_style_field_fill_color"]').empty();
        $('[name="ol_style_field_stroke_color"]').empty();
        $('[name="ol_style_field_stroke_width"]').empty();
        $('[name="ol_style_field_icon"').append('<option value=""></option>');
        $('[name="ol_style_field_fill_color"').append('<option value=""></option>');
        $('[name="ol_style_field_stroke_color"').append('<option value=""></option>');
        $('[name="ol_style_field_stroke_width"').append('<option value=""></option>');

        $.each(attributes, function (i, item) {
            $('[name="search[]"').append(
                '<option value="' + item + '"'
                + (search.indexOf(item) !== -1? ' selected ' : '')
                + '>' + item + '</option>'
            );
            $('[name="ol_style_field_icon"').append(
                '<option value="' + item + '"'
                + (ol_style_field_icon === item ? ' selected ' : '')
                + '>' + item + '</option>'
            );
            $('[name="ol_style_field_fill_color"').append(
                '<option value="' + item + '"'
                + (ol_style_field_fill_color === item ? ' selected ' : '')
                + '>' + item + '</option>'
            );
            $('[name="ol_style_field_stroke_color"').append(
                '<option value="' + item + '"'
                + (ol_style_field_stroke_color === item ? ' selected ' : '')
                + '>' + item + '</option>'
            );
            $('[name="ol_style_field_stroke_width"').append(
                '<option value="' + item + '"'
                + (ol_style_field_stroke_width === item ? ' selected ' : '')
                + '>' + item + '</option>'
            );
        });
    }
    
    function hideTypeOptions() {
        $('#projection_options').hide();
        $('#style_options').hide();
        $('#bing_options').hide();
        $('#mapquest_options').hide();
        $('#wms_options').hide();
        $('#wfs_options').hide();
        $('#gpx_options').hide();
        $('#kml_options').hide();
        $('#shapefile_options').hide();
        $('#postgis_options').hide();
        $('#geojson_options').hide();
        $('#geopackage_options').hide();
        $('#vector_options').hide();
        $('.vector-warning').hide();
    }
    
    function showTypeOptions(value) {
        hideTypeOptions();
        $('#' + value + '_options').show();
        if (value === 'wfs' || value === 'wms' || value === 'shapefile' || value === 'postgis') {
            $('#projection_options').show();
        }
        if (value === 'wfs' || value === 'gpx' || value === 'postgis' || value === 'geojson' || value === 'geopackage') {
            $('#style_options').show();
        }
        if (value === 'kml' || value === 'wfs' || value === 'gpx' || value === 'postgis' || value === 'geojson' || value === 'geopackage') {
            $('#vector_options').show();
            $('.vector-warning').show();
        }
        if ($('[name="id"]').val() && value === 'geojson') {
            attributes = $('[name="geojson_attributes"]').val().split(',');
            updateSelectAttribute();
        }
        if ($('[name="id"]').val() && value === 'postgis') {
            $('[name="postgis_attributes[]"] :selected').each(function (i, item) {
                attributes.push($(item).attr('value'));
            });
            updateSelectAttribute();
        }
        if ($('[name="id"]').val() && value === 'geopackage') {
            $('[name="geopackage_fields[]"] :selected').each(function (i, item) {
                attributes.push($(item).attr('value'));
            });
            updateSelectAttribute();
        }
    }
    
    showTypeOptions($('[name="type"]').val());
    $('[name="type"]').on('change', function () {
        showTypeOptions($(this).val());
    });
    
    var gpx_uploader = $("#gpx_filename").fileinput({
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
		maxFileCount: 1,
        allowedFileExtensions: ["gpx"]
    });
    gpx_uploader.on('filebatchselected', function(event, files) {
        
        var reader = new FileReader();
        var format = new ol.format.GPX();
        var result = [];
        var attrs;

        // Closure to capture the file information.
        reader.onload = (function(theFile) {
            return function(e) {
                result = format.readFeatures(e.target.result);
                if (result.length) {
                    attributes = [];
                    $.each(result, function (i, item) {
                        attrs = Object.keys(item.getProperties());
                        $.each(attrs, function (j, att) {
                            if (attributes.indexOf(att) === -1) attributes.push(att);
                        });
                    });
                    attributes.splice(attributes.indexOf('geometry'), 1);
                    $('.gpx-attributes').text("{{ trans('backoffice.attributes') }}: " + (attributes.join(',')));
                } else {
                    $('.gpx-attributes').text('');
                }
                updateSelectAttribute();
            };
        })(files[0]);

        // Read in the image file as a data URL.
        reader.readAsBinaryString(files[0]);
    });
    
    $('.getwmscapabilities').on('click', function (e) {
        e.preventDefault();
        
        var url = $('[name="wms_url"]').val();
        var version = $('[name="wms_version"]').val();
        var service = new $.fn.OGCService(url);
            url = service.getCapabilitiesUrl('WMS', version);
        $.post(BASE_URL + '/proxy', {url: url, _token: $('[name="_token"]').val()}, function (r) {
            
            // Call WMS GetCapabilities
            var service = new $.fn.OGCService(url);
            var result = service.parseWMSCapabilities(r, version);
            $('[name="wms_layers[]"').empty();
            $.each(result, function (i, group) {
                $.each(group.layers, function (i, l) {
                    $('[name="wms_layers[]"').append(
                        '<option value="' + l.name + '">' + l.title + '</option>'
                    );
                });
            });

        });
        
    });
    
    $('.getwfscapabilities').on('click', function (e) {
        e.preventDefault();
        
        var url = $('[name="wfs_url"]').val();
        var version = $('[name="wfs_version"]').val();
        var service = new $.fn.OGCService(url);
        
        url = service.getCapabilitiesUrl('WFS', version);
        $.post(BASE_URL + '/proxy', {url: url, _token: $('[name="_token"]').val()}, function (r) {
            
            var service = new $.fn.OGCService(url);
            var result = service.parseWFSCapabilities(r, version);
            $('[name="wfs_typename"').empty();
            tables = [];
            $.each(result, function (i, f) {
                $('[name="wfs_typename"').append(
                    '<option value="' + f.name + '">' + f.title + '</option>'
                );
                tables.push(f.name);
            });
            $('[name="wfs_typename"]').trigger('change');
        });
    });
    
    $('[name="wfs_typename"]').change('click', function (e) {
        e.preventDefault();
        
        var url = $('[name="wfs_url"]').val();
        var version = $('[name="wfs_version"]').val();
        var typename = $('[name="wfs_typename"]').val();
        var namespace = 'ms'; // TODO, check type of server
        var service = new $.fn.OGCService(url);
        
        // Call WFS GetCapabilities
        url = service.buildWFSGetFeatureUrl(version, typename, {'MAXFEATURES': 1});
        $.post(BASE_URL + '/proxy', {url: url, _token: $('[name="_token"]').val()}, function (r) {
                
            var result = service.parseWFSGetFeature(r, typename, namespace);
            if (result.length) {
                delete result[0]['bounds'];
                delete result[0]['geometry'];
                attributes = Object.keys(result[0]);
                $('.wfs-attributes').text("{{ trans('backoffice.attributes') }}: " + (Object.keys(result[0]).join(',')));
            } else {
                $('.wfs-attributes').text('');
            }
            updateSelectAttribute();
        });
    });
    
    var kml_uploader = $("#kml_filename").fileinput({
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
		maxFileCount: 1,
        allowedFileExtensions: ["kml"]
    });
    kml_uploader.on('filebatchselected', function(event, files) {
        
        var reader = new FileReader();
        var format = new ol.format.KML();
        var result = [];

        // Closure to capture the file information.
        reader.onload = (function(theFile) {
            return function(e) {
                result = format.readFeatures(e.target.result);
                if (result.length) {
                    attributes = Object.keys(result[0].getProperties());
                    attributes.splice(attributes.indexOf('geometry'), 1);
                    attributes.splice(attributes.indexOf('styleUrl'), 1);
                    $('.kml-attributes').text("{{ trans('backoffice.attributes') }}: " + (attributes.join(',')));
                } else {
                    $('.kml-attributes').text('');
                }
                updateSelectAttribute();
            };
        })(files[0]);

        // Read in the image file as a data URL.
        reader.readAsBinaryString(files[0]);
    });
    
    $("#shapefile_filename").fileinput({
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
		maxFileCount: 1,
        allowedFileExtensions: ['zip'],
        showPreview: false
    });
    
    $('[name="postgis_schema"]').on('change', function () {
        var form = $(this).closest('form');
        var schemaname = $('[name="postgis_schema"]').val();
        $.post("{{ url('admin/layers/postgis/table/list') }}/" + schemaname, form.serialize(), function (r) {
            if (r.success) {
                $('[name="postgis_table"]').empty();
                tables = [];
                $.each(r.result, function (i, table) {
                    $('[name="postgis_table"]').append(
                        '<option value="' + table.name + '">' + table.name + '</option>'
                    );
                    tables.push(table.name);
                });
                $('[name="postgis_table"]').trigger('change');
            }
        });
    });
    
    $('[name="postgis_table"]').on('change', function () {
        var form = $(this).closest('form');
        var schemaname = $('[name="postgis_schema"]').val();
        var tablename = $('[name="postgis_table"]').val();
        var postgis_field = $('[name="postgis_field"]').val();
        var postgis_attributes = attributes;
        $.post("{{ url('admin/layers/postgis/column/list') }}/" + schemaname + '/' + tablename, form.serialize(), function (r) {
            if (r.success) {
                attributes = [];
                $('[name="postgis_field"]').empty();
                $.each(r.result, function (i, column) {
                    $('[name="postgis_field"]').append(
                        '<option value="' + column.name + '"'
                        + (column.name === postgis_field ? ' selected ' : '')
                        + '>' + column.name + '</option>'
                    );
                    attributes.push(column.name);
                });
                $('[name="postgis_attributes[]"]').empty();
                $.each(r.result, function (i, schema) {
                    $('[name="postgis_attributes[]"]').append(
                        '<option value="' + schema.name + '"'
                        + (postgis_attributes.indexOf(schema.name) !== -1 ? ' selected ' : '')
                        + '>' + schema.name + '</option>'
                    );
                });
                updateSelectAttribute();
            }
        });
    });
    
    $('.getpostgisschemas').on('click', function (e) {
        e.preventDefault();
        
        $('.v-error-postgis_user').text('');
        $('.v-error-postgis_pass').text('');
        if ($('[name="postgis_user"').val() === '') {
            $('.v-error-postgis_user').text("{{ trans('validation.required', ['attribute' => trans('backoffice.postgis_user')]) }}");
        }
        if ($('[name="postgis_pass"').val() === '') {
            $('.v-error-postgis_pass').text("{{ trans('validation.required', ['attribute' => trans('backoffice.postgis_pass')]) }}");
        }
        if ($('[name="postgis_user"').val() === '' || $('[name="postgis_pass"').val() === '') return;
        
        var form = $(this).closest('form');
        $.post("{{ url('admin/layers/postgis/schema/list') }}", form.serialize(), function (r) {
            if (r.success) {
                $('[name="postgis_schema"]').empty();
                $.each(r.result, function (i, schema) {
                    $('[name="postgis_schema"]').append(
                        '<option value="' + schema.name + '">' + schema.name + '</option>'
                    );
                });
                $('[name="postgis_schema"]').trigger('change');
            }
        });
    });
    
    var geopackage_tables = [];
    var geopackage_uploader = $("#geopackage_filename").fileinput({
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
		maxFileCount: 1,
        uploadUrl: "{{ url('admin/layers/geopackage_upload') }}",
        allowedFileExtensions: ['gpkg'],
        showPreview: false,
        uploadExtraData: function() {
            return {
                '_token': $('[name="_token"]').val()
            };
        }
    });
    geopackage_uploader.on('filebatchselected', function(event, files) {
        geopackage_uploader.fileinput('upload');
    });
    
    // Populate fields options
    $('[name="geopackage_table"').on('change', function () {
        var selected = $(this).val();
        var geopackage_fields = attributes;
        $('[name="geopackage_fields[]"').empty();
        $.each(geopackage_tables, function (i, item) {
            if (item.table_name === selected) {
                attributes = [];
                $.each(item.columns, function (i, column) {
                    $('[name="geopackage_fields[]"').append(
                        '<option value="' + column.column_name + '"'
                        + (geopackage_fields.indexOf(column.column_name) !== -1 ? ' selected ' : '')
                        + '>' + column.column_name + '</option>'
                    );
                    attributes.push(column.column_name);
                });
                updateSelectAttribute();
            }
        });
    });
    geopackage_uploader.on('filebatchuploadsuccess', function(event, data, previewId, index) {
        var form = data.form, files = data.files, extra = data.extra,
            response = data.response, reader = data.reader;
        if (response.success) {
            geopackage_tables = response.result.tables;
            
            // Populate table options
            $('[name="geopackage_table"').empty();
            tables = [];
            $.each(response.result.tables, function (i, item) {
                $('[name="geopackage_table"').append(
                    '<option value="' + item.table_name + '">' + item.table_name + '</option>'
                );
                tables.push(item.table_name);
            });
            $('[name="geopackage_table"').trigger('change');
        }
    });
    
    // Correct attributes user input
    var normalizeAttributes = function (input) {
        input = input.replace(/^\s+|\s+$/g, ''); // trim
        input = input.toLowerCase();

        // remove accents, swap ñ for n, etc
        var from = "ãàáäâẽèéëêìíïîõòóöôùúüûñç·/-:;|";
        var to   = "aaaaaeeeeeiiiiooooouuuunc_____|";
        for (var i=0, l=from.length ; i<l ; i++) {
            input = input.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
        }

        input = input.replace(/[^a-z0-9_,]/g, '') // remove invalid chars
          .replace(/\s+/g, '') // collapse whitespace and replace by -
          .replace(/-+/g, '') // collapse dashes
          .replace(/_{3,}/g, '__'); // collapse 3 or more underscores

        return input;
    };
    $('[name="geojson_attributes"]').on('keyup', function (e) {
        $(this).val(normalizeAttributes($(this).val()));
        attributes = $(this).val().split(',');
        updateSelectAttribute();
    });
    
    $("#ol_style_static_icon").fileinput({
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
		maxFileCount: 1
    });
    
    var icons_uploader = $("#image_uploader").fileinput({
        language: "pt",
        uploadUrl: "{{ url('admin/layers/upload/'.$layer->id) }}",
        allowedFileExtensions: ["jpg", "png", "gif"],
        maxFileSize: 2000,
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
        uploadExtraData: function() {
            return {
                '_token': $('[name="_token"]').val()
            };
        }
    });
    icons_uploader.on('filebatchselected', function(event, files) {
        icons_uploader.fileinput('upload');
    });
    
    var csv_uploader = $("#csv_uploader").fileinput({
        language: "pt",
        uploadUrl: "{{ url('admin/layers/import/csv/'.$layer->id) }}",
        allowedFileExtensions: ["csv"],
        maxFileSize: 2000,
        showCaption: false,
        overwriteInitial: true,
        showUpload: false,
        showRemove: false,
        uploadExtraData: function() {
            return {
                '_token': $('[name="_token"]').val()
            };
        }
    });
    csv_uploader.on('filebatchselected', function(event, files) {
        csv_uploader.fileinput('upload');
        window.location.reload();
    });

    $('#icons .delete-image').on('click', function() {
        var me = $(this);
        var resp = confirm('Destroy image?');
        if (resp) {
            $.get("{{ url('admin/layers/'.$layer->id.'/delete') }}/" + $(this).data('item').split(/[\\/]/).pop(), function (resp) {
                if (resp.success) {
                    me.parent().remove();
                } else {
                    alert('Could not destroy image!');
                }
            });
        }
    });
    
    $('[name="ol_style_static_fill_color"]').parent().colorpicker();
    $('[name="ol_style_static_stroke_color"]').parent().colorpicker();
    
    var form = new Form($, '#formLayer', {files: [
        '#gpx_filename',
        '#kml_filename',
        '#shapefile_filename',
        '#geopackage_filename',
        '#ol_style_static_icon'
    ]});
    
    @if($layer->type === 'geojson')
    var EditMap = function () {
        var raster = new ol.layer.Tile({
            source: new ol.source.OSM()
        });

        var map = new ol.Map({
            layers: [raster],
            target: 'map',
            view: new ol.View({
                center: [0, 0],
                zoom: 2
            })
        });

        $('#features').removeClass('active');

        var format = new ol.format.GeoJSON();
        var features = new ol.Collection();

        var featureOverlay = new ol.layer.Vector({
            source: new ol.source.Vector({features: features}),
            style: new ol.style.Style({
                fill: new ol.style.Fill({
                    color: 'rgba(255, 255, 255, 0.2)'
                }),
                stroke: new ol.style.Stroke({
                    color: '#ffcc33',
                    width: 2
                }),
                image: new ol.style.Circle({
                    radius: 7,
                    fill: new ol.style.Fill({
                        color: '#ffcc33'
                    })
                })
            })
        });
        featureOverlay.setMap(map);

        var select = new ol.interaction.Select({
            wrapX: false
        });
        map.addInteraction(select);

        var modify = new ol.interaction.Modify({
            features: features,
            // the SHIFT key must be pressed to delete vertices, so
            // that new vertices can be drawn at the same position
            // of existing vertices
            deleteCondition: function(event) {
                return ol.events.condition.shiftKeyOnly(event) &&
                    ol.events.condition.singleClick(event);
            }
        });
        map.addInteraction(modify);

        var draw; // global so we can remove it later
        function addInteraction(type) {
            draw = new ol.interaction.Draw({
              features: features,
              type: /** @type {ol.geom.GeometryType} */ (type)
            });
            map.addInteraction(draw);
        }

        var editingFeature;

        /**
         * Let user change the geometry type.
         * @param {Event} e Change event.
         */
        $('[name="geojson_geomtype"]').on('change', function(e) {
            map.removeInteraction(draw);
            addInteraction($(this).val());
        });

        addInteraction($('[name="geojson_geomtype"]').val());

        function toggleInteraction(active) {
            $('#toggleInteraction button').removeClass('active');
            $('#toggleInteraction #' + active).addClass('active');
            if (active === 'interactionCreate') {
                select.setActive(false);
                modify.setActive(false);
                draw.setActive(true);
            }
            if (active === 'interactionModify') {
                select.setActive(false);
                modify.setActive(true);
                draw.setActive(false);
            }
            if (active === 'interactionSelect') {
                select.setActive(true);
                modify.setActive(false);
                draw.setActive(false);
            }
        }

        $('#toggleInteraction button').on('click', function () {
            toggleInteraction($(this).attr('id'));
        });
        toggleInteraction('interactionCreate');

        function updateFeaturesField() {
            setTimeout(function() {
                var json = format.writeFeatures(featureOverlay.getSource().getFeatures());
                json = json.substring(0, json.length - 1);
                json += ',"crs":{"type":"name","properties":{"name":"EPSG:' + $('[name="projection_id"').val() + '"}}' + '}';
                $('[name="geojson_features"]').val(json);
                $('[name="geojson_features"]').text(json);
            }, 100);
        }

        function setAttributes() {
            var attributes = $('[name="geojson_attributes"]').val().split(',');
            $('#modal .modal-body').empty();
            $.each(attributes, function(i, item) {
                $('#modal .modal-body').append(Mustache.render($('#feature_attribute_tpl').html(), {name: item, value: editingFeature.get(item)}));
            });
            $('#modal').modal('show');

        }
        select.on('select', function(e) {
            editingFeature = select.getFeatures().item(0);
            if (editingFeature) {
                setAttributes();
                select.getFeatures().clear();
            }
        });
        modify.on('modifyend', function (e, el) {
            updateFeaturesField();
        });
        draw.on('drawend', function(e) {
            editingFeature = e.feature;
            setAttributes();
        });

        $('#modal form').on('submit', function (e) {
            e.preventDefault();
            var values = $(this).serializeArray();
            $.each(values, function(i, attribute) {
                editingFeature.set(attribute.name, attribute.value);
            });
            updateFeaturesField();
            $('#modal').modal('hide');
        });

        $.get('{{ url('/storage/layer/' . $layer->id . '/geojson.json') }}', function (response) {
            var items = format.readFeatures(response, {featureProjection: 'EPSG:' + $('[name="projection_id"]').val()});
            $.each(items, function (i, feature) {
                features.push(feature);
            });
        });
        
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href=#' + url.split('#')[1] + ']').tab('show');
        } 

        // Change hash for page-reload
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            window.location.hash = e.target.hash;
        });
    };

    EditMap();
    
    @endif
    
</script>
@stop