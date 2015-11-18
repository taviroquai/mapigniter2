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
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/ol.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/map.css') }}" rel="stylesheet">
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
                        <a class="dropdown-toggle" href="#" data-toggle="collapse" data-target="#content-items" aria-expanded="false" aria-controls="content">
                            {{ trans('layout.link_contents') }}
                            <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu" id="content-items">
                        @foreach(App\Content::getPublishedItems() as $item)
                            <li><a href="{{ url($item->seo_slug) }}">{{ $item->title }}</a></li>
                        @endforeach
                        </ul>
                    </li>
                    
                </ul>
                
                <form class="navbar-form navbar-right">
                    <select id="selectIdiom" class="form-control" title="{{ trans('layout.select_idiom') }}">
                    @foreach (\App\Idiom::getAvailableIdioms() as $item)
                        <option value="{{ $item }}"
                            @if(\App::getLocale() === $item) selected @endif
                            >{{ $item }}</option>
                    @endforeach
                    </select>
                </form>
                
                <ul class="nav navbar-nav navbar-right">
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
      
    <div id="map"></div>
    
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
                        <div ng-repeat="group in groupLayers">
                            <h4><span class="fa fa-list"> <% group.title %></span></h4>
                            <ul id="<% group.id %>" class="list-group">
                                <li ng-repeat="l in group.layers" class="list-group-item checkbox">
                                    <label>
                                        <input ng-model="l.visible" ng-click="toggleLayer(l)"
                                            type="checkbox" checked="<% l.visible %>" /><% l.content.title %>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div ng-controller="ngFeatureInfo" ng-cloak>
                    <div ng-bind-html="info"></div>
                </div>
                
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
                    
                    <p class="no-results" ng-show="hasResults && results.length === 0">{{ trans('layout.feature_no_results') }}</p>
                    
                    <div ng-show="hasResults">
                        <ul>
                            <li ng-repeat="item in results">
                                <a ng-click="locateItem(item)"
                                    class="btn btn-default btn-xs"
                                    data-name="<% item.name %>"
                                    data-index="<% item.index %>"><% item.label %> (<% item.layer %>)</a>
                            </li>
                        </ul>
                    </div>
                    
                </div>
                
                @section('content')
                @show
            
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    
    @section('script')
    <script src="{{ asset('assets/js/mustache.min.js') }}"></script>
    <script src="{{ asset('assets/js/proj4.js') }}"></script>
    <script src="{{ asset('assets/js/ol-debug.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/angular.min.js') }}"></script>
    <script src="{{ asset('assets/js/angular-sanitize.min.js') }}"></script>
    <script src="{{ asset('assets/js/ngMap.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngFeatureInfo.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngLayerSwitcher.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngSearchResults.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/ngNavigationToolbar.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        
        angular.module('ngMap').value('config', { 
            baseURL: '{!! url('/') !!}',
            mapId: {{ $map ? $map->id : null }}
        });
        
    </script>
    @show
    
  </body>
</html>
