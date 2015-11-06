<!DOCTYPE html>
<html lang="en">
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
                
                <ul class="nav navbar-nav navbar-right">
                    @if (!Auth::check())
                    <li><a href="{{ url('auth/login') }}">{{ trans('layout.link_login') }}</a></li>
                    <li><a href="{{ url('auth/register') }}">{{ trans('layout.link_register') }}</a></li>
                    @else
                    <li><a href="{{ url('admin/dashboard') }}">{{ trans('layout.link_admin') }}</a></li>
                    <li><a href="{{ url('auth/logout') }}">{{ trans('layout.link_logout') }}</a></li>
                    @endif
                </ul>
                <form class="navbar-form navbar-right" id="searchForm">
                    <input id="search" type="text" class="form-control" placeholder="{{ trans('layout.search') }}">
                </form>
                <ul id="mapNavigation" class="nav navbar-nav navbar-right">
                    <li>
                        <a class="btn action-nav-extent" title="{{ trans('layout.map_navigation_full') }}">
                            <i class="fa fa-globe"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn action-nav-reset" title="{{ trans('layout.map_navigation_reset') }}">
                            <i class="fa fa-hand-paper-o"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn action-nav-zoombox" title="{{ trans('layout.map_navigation_zoomin') }}">
                            <i class="fa fa-search-plus"></i>
                        </a>
                    <li>
                        <a class="btn action-nav-zoomout" title="{{ trans('layout.map_navigation_zoomout') }}">
                            <i class="fa fa-search-minus"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn action-nav-previous" title="{{ trans('layout.map_navigation_previous') }}">
                            <i class="fa fa-mail-reply"></i>
                        </a>
                    </li>
                    <li>
                        <a class="btn action-nav-next" title="{{ trans('layout.map_navigation_next') }}">
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
                <div id="layerSwitcher"></div>
                <div id="featureInfo"></div>
                <div id="featureSearchResults" style="display: none">
                    <h4>{{ trans('layout.feature_results_title') }}</h4>
                    <p class="no-results">{{ trans('layout.feature_no_results') }}</p>
                    <button id="featureSearchClear" class="btn btn-primary btn-xs pull-right">{{ trans('layout.feature_results_clear') }}</button>
                    <ul></ul>
                </div>
                
                @section('content')
                @show
            
            </div>
        </div>
    </div>
    <script type="text/html" id="layer_switcher_group_tpl">
        @include('mustache.layer_switcher.group')
    </script>
    <script type="text/html" id="layer_switcher_item_tpl">
        @include('mustache.layer_switcher.item')
    </script>
    <script type="text/html" id="search_result_item_tpl">
        @include('mustache.search.item')
    </script>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="{{ asset('assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/mustache.min.js') }}"></script>
    
    @section('script')
    <script src="{{ asset('assets/js/proj4.js') }}"></script>
    <script src="{{ asset('assets/js/ol-debug.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/js/map.js') }}" type="text/javascript"></script>
    <script type="text/javascript">
        @if ($map)
        $.getJSON('{{ url("maps/{$map->id}/config") }}', function(config) {
            config['base_url'] = '{{ url("/") }}'
            var app = new Map($, config);
        });
        @else
        alert('Please create a map.');
        @endif
    </script>
    @show
    
  </body>
</html>
