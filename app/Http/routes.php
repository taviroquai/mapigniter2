<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Model bindings
Route::model('user', 'App\User');
Route::model('role', 'App\Role');
Route::model('permission', 'App\Permission');
Route::model('content', 'App\Content');
Route::model('brand', 'App\Brand');
Route::model('page', 'App\Page');
Route::model('projection', 'App\Projection');
Route::model('map', 'App\Map');
Route::model('layer', 'App\Layer');
Route::model('layeritem', 'App\Layeritem');

Route::get('/old', 'HomeController@index');

// Load user pages routes
App\Http\Controllers\HomeController::loadUserPagesRoutes();

// Set idiom
Route::get('/idiom/{idiom}', 'IdiomController@setIdiom');

// WebGIS
Route::get('/maps/{map}', 'MapController@getMap');
Route::get('/maps/{map}/config', 'MapController@getConfig');
Route::get('/proxy/{url}', 'MapController@proxyRequest');

// Auth routes
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// LDAP auth
Route::get('ldap/login', 'Ldap\AuthController@getLogin');
Route::post('ldap/login', 'Ldap\AuthController@postLogin');

// Backoffice routes
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'middleware' => ['auth', 'permission']], function () {
    
    Route::get('dashboard', 'DashboardController@index');
    
    // Projection
    Route::get('/projections', 'ProjectionController@json');
    Route::get('/projections/list', 'ProjectionController@index');
    Route::get('/projections/form/{projection?}', 'ProjectionController@form');
    Route::post('/projections', 'ProjectionController@save');
    Route::get('/projections/delete/{projection}', 'ProjectionController@delete');
    Route::get('/projections/import/{srid}', 'ProjectionController@importFromSpatialReference');
    
    // Map
    Route::get('/maps', 'MapController@json');
    Route::get('/maps/list', 'MapController@index');
    Route::get('/maps/form/{map?}', 'MapController@form');
    Route::post('/maps', 'MapController@save');
    Route::get('/maps/ownership/{map}', 'MapController@formOwnership');
    Route::post('/maps/ownership/{map}', 'MapController@saveOwnership');
    Route::get('/maps/copy/{map}', 'MapController@copy');
    Route::get('/maps/delete/{map}', 'MapController@delete');
    Route::post('/maps/upload/{map}', 'MapController@upload');
    Route::get('/maps/{map}/delete/{filename}', 'MapController@deleteGalleryImage');
    Route::post('/maps/attachment/{map}', 'MapController@uploadAttachment');
    Route::get('/maps/{map}/attachment/delete/{filename}', 'MapController@deleteAttachment');
    Route::post('/maps/layeritem/add', 'MapController@addLayeritem');
    Route::get('/maps/{map}/layeritem/del/{layeritem}', 'MapController@delLayeritem');
    Route::get('/maps/{map}/layeritem/orderup/{layeritem}', 'MapController@orderupLayeritem');
    Route::get('/maps/{map}/layeritem/orderdown/{layeritem}', 'MapController@orderdownLayeritem');
    
    // Layer
    Route::get('/layers', 'LayerController@json');
    Route::get('/layers/list', 'LayerController@index');
    Route::get('/layers/form/{layer?}', 'LayerController@form');
    Route::post('/layers', 'LayerController@save');
    Route::get('/layers/ownership/{layer}', 'LayerController@formOwnership');
    Route::post('/layers/ownership/{layer}', 'LayerController@saveOwnership');
    Route::get('/layers/copy/{layer}', 'LayerController@copy');
    Route::get('/layers/delete/{layer}', 'LayerController@delete');
    Route::post('/layers/upload/{layer}', 'LayerController@upload');
    Route::get('/layers/{layer}/delete/{filename}', 'LayerController@deleteIconImage');
    Route::post('/layers/import/csv/{layer}', 'LayerController@importCSV');
    
    // Content
    Route::get('/contents', 'ContentController@json');
    Route::get('/contents/list', 'ContentController@index');
    Route::get('/contents/form/{content?}', 'ContentController@form');
    Route::post('/contents', 'ContentController@save');
    Route::get('/contents/ownership/{content}', 'ContentController@formOwnership');
    Route::post('/contents/ownership/{content}', 'ContentController@saveOwnership');
    Route::get('/contents/copy/{content}', 'ContentController@copy');
    Route::get('/contents/delete/{content}', 'ContentController@delete');
    Route::post('/contents/upload/{content}', 'ContentController@upload');
    Route::get('/contents/{content}/delete/{filename}', 'ContentController@deleteGalleryImage');
    Route::post('/contents/attachment/{content}', 'ContentController@uploadAttachment');
    Route::get('/contents/{content}/attachment/delete/{filename}', 'ContentController@deleteAttachment');
    
    // Views
    Route::get('/pages', 'PageController@json');
    Route::get('/pages/list', 'PageController@index');
    Route::get('/pages/form/{page?}', 'PageController@form');
    Route::post('/pages', 'PageController@save');
    Route::get('/pages/delete/{page}', 'PageController@delete');
    
    // User
    Route::get('/users', 'UserController@json');
    Route::get('/users/list', 'UserController@index');
    Route::get('/users/form/{user?}', 'UserController@form');
    Route::post('/users', 'UserController@save');
    Route::get('/users/delete/{user}', 'UserController@delete');
    Route::get('/profile', 'UserController@profile');
    Route::post('/profile', 'UserController@profileSave');
    
    // Role
    Route::get('/roles', 'RoleController@json');
    Route::get('/roles/list', 'RoleController@index');
    Route::get('/roles/form/{role?}', 'RoleController@form');
    Route::post('/roles', 'RoleController@save');
    Route::get('/roles/delete/{role}', 'RoleController@delete');
    
    // Permission
    Route::get('/permissions', 'PermissionController@json');
    Route::get('/permissions/list', 'PermissionController@index');
    Route::get('/permissions/form/{permission?}', 'PermissionController@form');
    Route::post('/permissions', 'PermissionController@save');
    Route::get('/permissions/delete/{permission}', 'PermissionController@delete');
    Route::get('/permissions/download', 'PermissionController@downloadLogs');
    
    // Branding
    Route::get('/brands', 'BrandController@json');
    Route::get('/brands/list', 'BrandController@index');
    Route::get('/brands/form/{brand?}', 'BrandController@form');
    Route::post('/brands', 'BrandController@save');
    Route::get('/brands/delete/{brand}', 'BrandController@delete');
    
    // Visits
    Route::get('visits', 'VisitController@json');
    Route::get('visits/list', 'VisitController@index');
    Route::get('visits/totals/{date_start}/{date_end}', 'VisitController@visitsTotalsJson');
    
});

