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
        'ekko-lightbox.min.css',
        'lightbox-dark.css',
        'ol.css',
        'map.css'
    ], 'public/assets/css/production_map.css');
    
    mix.scripts([
        'jquery.min.js',
        'bootstrap.min.js',
        'ekko-lightbox.min.js',
        'proj4.js',
        'ol.js',
        'lunr.min.js',
        'angular.min.js',
        'ngMap.js',
        'ngIdiom.js',
        'ngContent.js',
        'ngFeatureInfo.js',
        'ngLayerSwitcher.js',
        'ngSearchResults.js',
        'ngNavigationToolbar.js',
        'ngPrint.js'
    ], 'public/assets/js/production_map.js');
    
});
