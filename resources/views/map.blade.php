<!DOCTYPE html>
<html lang="en" ng-app="ngMap">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    @section('seo')
    <title>{{ $brand->name }}</title>
    @show

    <!-- Bootstrap core CSS -->
    @if(env('APP_ENV') === 'local')
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" media="screen,projection">
    <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/ekko-lightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/lightbox-dark.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/ol.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/map.css') }}" rel="stylesheet" media="screen,projection">
    @else
    <link href="{{ asset('assets/css/production_map.css') }}" rel="stylesheet" media="screen,projection">
    @endif
    <link href="{{ asset('assets/css/map_print.css') }}" rel="stylesheet" media="print">
    <link href="{{ asset('storage/style.css') }}" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">{{ trans('layout.menu') }}</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('/') }}">{{ $brand->name }}</a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar">
                    <li><a href="#" data-toggle="collapse" data-target="#content" aria-expanded="false" aria-controls="content">{{ trans('layout.link_layers') }}</a></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="collapse" data-target="#map-items" aria-expanded="false" aria-controls="content">
                            {{ trans('layout.link_maps') }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" id="map-items">
                        @foreach(App\Map::getPublishedItems() as $item)
                            <li><a href="{{ url('maps/' . $item->id) }}">{{ $item->content->title }}</a></li>
                        @endforeach
                        </ul>
                    </li>
                    <li class="dropdown" ng-controller="ngContent" 
                        ng-init="target = '#content-modal'">
                        <input type="hidden" ng-model="url" />
                        <a class="dropdown-toggle" href="#" data-toggle="collapse" data-target="#content-items" aria-expanded="false" aria-controls="content">
                            {{ trans('layout.link_contents') }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" id="content-items">
                        @foreach(App\Content::getPublishedItems() as $item)
                        <li><a ng-click="url = '{{ url($item->seo_slug) }}'; title = '{{ $item->title }}'; show();"
                                href="">{{ $item->title }}</a>
                        </li>
                        @endforeach
                        </ul>
                    </li>
                    
                </ul>
                
                <form ng-controller="ngIdiom" ng-cloak
                    class="navbar-form navbar-right">
                    <select ng-model="selected"
                            ng-options="item for item in idioms"
                            ng-change="changeIdiom()"
                        class="form-control input-sm" title="{{ trans('layout.select_idiom') }}"></select>
                </form>
                
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="collapse" data-target="#print" aria-expanded="false" aria-controls="content">
                            {{ trans('layout.link_print') }}
                            <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu" id="print" ng-controller="ngPrint">
                            <form ng-submit="print()" class="form-inline">
                                <div class="form-group">
                                    <select ng-model="selected"
                                        ng-change="updatePrintLayout()"
                                        class="form-control input-sm" title="{{ trans('layout.select_map_layout') }}">
                                        <option value="screen" selected="selected">{{ trans('layout.map_layout_screen') }}</option>
                                        <option value="a4v">{{ trans('layout.map_layout_a4v') }}</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary">Print</button>
                            </form>
                        </div>
                    </li>
                    @if (!Auth::check())
                    <li><a href="{{ url('auth/login') }}">{{ trans('layout.link_login') }}</a></li>
                    <li><a href="{{ url('auth/register') }}">{{ trans('layout.link_register') }}</a></li>
                    @else
                    <li><a href="{{ url('admin/dashboard') }}">{{ trans('layout.link_admin') }}</a></li>
                    <li><a href="{{ url('auth/logout') }}">{{ trans('layout.link_logout') }}</a></li>
                    @endif
                </ul>
                
                <ul ng-controller="ngNavigationToolbar" ng-cloak
                    class="nav navbar-nav navbar-right">
                    <li>
                        <a ng-click="fullView()"
                            class="btn" title="{{ trans('layout.map_navigation_full') }}">
                            <i class="fa fa-globe"></i>
                        </a>
                    </li>
                    <li>
                        <a ng-click="reset()"
                            class="btn" title="{{ trans('layout.map_navigation_reset') }}">
                            <i class="fa fa-hand-paper-o"></i>
                        </a>
                    </li>
                    <li>
                        <a ng-click="zoomBox()" ng-class="{'active': zoomBoxEnable}"
                            class="btn" title="{{ trans('layout.map_navigation_zoomin') }}">
                            <i class="fa fa-search-plus"></i>
                        </a>
                    <li>
                        <a ng-click="zoomOut()"
                            class="btn" title="{{ trans('layout.map_navigation_zoomout') }}">
                            <i class="fa fa-search-minus"></i>
                        </a>
                    </li>
                    <li>
                        <a ng-click="previousView()"
                            class="btn" title="{{ trans('layout.map_navigation_previous') }}">
                            <i class="fa fa-mail-reply"></i>
                        </a>
                    </li>
                    <li>
                        <a ng-click="nextView()"
                            class="btn" title="{{ trans('layout.map_navigation_next') }}">
                            <i class="fa fa-mail-forward"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="map-container">
          <div id="map"></div>
    </div>
      
    <div id="print-version"></div>
    
    <div id="content" class="container collapse in">
        <div class="row">
            <div class="col-md-12">
                
                <div ng-controller="ngLayerSwitcher" ng-cloak>
                    <div id="baseLayerSwitcher">
                        <h4><span class="fa fa-list"> {{ trans('layout.base_layers') }}</span></h4>
                        <select class="form-control" 
                            ng-options="item.content.title for item in baseLayers"
                            ng-model="baseLayer"
                            ng-change="selectedBaseLayer()">
                        </select>
                    </div>
                    <div id="layerSwitcher">
                        <div ng-repeat="g in groupLayers">
                            <h4 ng-click="toggleGroup(g)"><span class="fa fa-list"> <span ng-bind="g.title"></span></span></h4>
                            <ul class="list-group" ng-show="g.visible">
                                <li ng-repeat="l in g.layers" class="list-group-item checkbox">
                                    <label>
                                        <input ng-model="l.visible" ng-click="toggleLayer(l)"
                                            type="checkbox" ng-checked="l.visible" />
                                        <span ng-bind="l.content.title"></span>
                                    </label>
                                    <span ng-show="l.content.seo_description.length"
                                        class="pull-right layer-details-toggle"
                                        data-toggle="collapse"
                                        data-target=".layer-details-@{{ l.id }}"><span class="caret"></span></span>
                                    <a ng-click="zoomLayer(l)" ng-show="l.ol.getVisible()"
                                        title="Zoom to layer extent"
                                        class="btn btn-xs pull-right"><i class="fa fa-expand"></i></a>
                                    <div class="collapse layer-details-@{{ l.id }}">
                                        <div ng-bind="l.content.seo_description"></div>
                                        <img ng-show="l.ol.get('legendURL')" ng-src="@{{ l.ol.get('legendURL') }}" />
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <ul class="list-group" ng-show="layers.length">
                            <li ng-repeat="l in layers" class="list-group-item checkbox">
                                <label>
                                    <input ng-model="l.visible" ng-click="toggleLayer(l)"
                                        type="checkbox" ng-checked="l.visible" />
                                    <span ng-bind="l.content.title"></span>
                                </label>
                                <span ng-show="l.content.seo_description.length"
                                    class="pull-right layer-details-toggle"
                                    data-toggle="collapse"
                                    data-target=".layer-details-@{{ l.id }}"><span class="caret"></span></span>
                                <a ng-click="zoomLayer(l)" ng-show="l.ol.getVisible()"
                                    title="Zoom to layer extent"
                                    class="btn btn-xs pull-right"><i class="fa fa-expand"></i></a>
                                <div class="collapse layer-details layer-details-@{{ l.id }}">
                                    <div ng-bind="l.content.seo_description"></div>
                                    <img ng-show="l.ol.get('legendURL')" ng-src="@{{ l.ol.get('legendURL') }}" />
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div id="search" class="row">
            <div class="col-md-12">
                
                <div ng-controller="ngSearchResults" ng-cloak>
                    <h4>{{ trans('layout.search_title') }}</h4>
                    <form ng-submit="doSearch()">
                        <div class="input-group">
                            <input ng-model="query"
                                name="query"
                                type="text"
                                class="form-control"
                                placeholder="{{ trans('layout.search') }}" />
                            <div class="input-group-btn">
                                <button ng-hide="hasResults"
                                    type="submit" class="btn btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                                <button ng-show="hasResults"
                                    ng-click="clearResults(); query = ''" 
                                    class="btn btn-default">
                                    <i class="fa fa-remove"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <p class="no-results" ng-show="hasResults && results.length === 0">
                        {{ trans('layout.feature_no_results') }}
                    </p>
                    
                    <div ng-show="hasResults">
                        <ul id="search-results">
                            <li ng-repeat="item in results">
                                <a ng-click="locateItem(item)" class="btn btn-default btn-xs">
                                    <span ng-bind="item.label"></span> 
                                    (<span ng-bind="item.name"></span>)
                                </a>
                            </li>
                        </ul>
                    </div>
                    
                </div>
            </div>
        </div>
        
        <div id="feature-info" class="row">
            <div class="col-md-12">
                
                <div ng-controller="ngFeatureInfo" ng-cloak>
                    <div ng-show="item">
                        <h4>{{ trans('layout.featureinfo_title') }}</h4>
                        <button ng-click="clearInfo()" 
                            class="btn btn-danger btn-xs pull-right feature-info-clear">
                            {{ trans('layout.clear') }}
                        </button>
                        <div compile="template"></div>
                    </div>
                </div>
                
            </div>
        </div>
        
        <div id="content-section" class="row">
            <div class="col-md-12">
                
                @section('content')
                @show
            
            </div>
        </div>
    </div>
    
    <!-- Content modal -->
    <div id="content-modal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"></h4>
                </div>
                <div class="modal-body">
                    <p>One fine body&hellip;</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    @if(env('APP_ENV') === 'local')
    <script src="{{ asset('assets/js/jquery.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ekko-lightbox.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/buffer.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/wkx.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/proj4.js') }}"  type="text/javascript"></script>
    <script src="{{ asset('assets/js/ol.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/lunr.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/angular.min.js') }}"  type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngMap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngIdiom.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngContent.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngFeatureInfo.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngLayerSwitcher.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngSearchResults.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngNavigationToolbar.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngPrint.js') }}" type="text/javascript"></script>
    @else
    <script src="{{ asset('assets/js/production_map.js') }}" type="text/javascript"></script>
    @endif
    
    @section('script')
    <script type="text/javascript">
        
        $(document).delegate('*[data-toggle="lightbox"]:not([data-gallery="navigateTo"])', 'click', function(e) {
            e.preventDefault();
            return $(this).ekkoLightbox();
        });
        
        angular.module('ngMap').value('config', { 
            baseURL: '{!! url('/') !!}',
            idioms: {!! json_encode(\App\Idiom::getAvailableIdioms()) !!},
            idiomId: '{{ \App::getLocale() }}',
            mapId: {{ $map ? $map->id : null }}
        });
        
    </script>
    @show
    
  </body>
</html>
