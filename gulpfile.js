var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */
elixir.config.assetsPath = 'public/assets/'; //trailing slash required.

elixir(function(mix) {
    //mix.sass('app.scss');
    
    mix.styles([
        'bootstrap.min.css',
        'font-awesome.min.css',
        'ol.css',
        'map.css'
    ], 'public/assets/css/production_map.css');
    
    mix.scripts([
        'jquery.min.js',
        'bootstrap.min.js',
        'proj4.js',
        'ol-debug.js',
        'lunr.min.js',
        'angular.min.js',
        'ngMap.js',
        'ngIdiom.js',
        'ngFeatureInfo.js',
        'ngLayerSwitcher.js',
        'ngSearchResults.js',
        'ngNavigationToolbar.js',
        'ngPrint.js'
    ], 'public/assets/js/production_map.js');
    
});
