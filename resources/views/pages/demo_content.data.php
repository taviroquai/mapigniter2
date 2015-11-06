<?php

// Redirect if does content not found
if (!$content = App\Content::where('seo_slug', \Route::input('slug'))->first()) {
    header('Location: '.url('page/404')); die();
}

// Save visit
$request = \Request::instance();
$user = \Auth::user();
$visit = new App\Visit([
    'http_url' => $request->fullUrl(),
    'http_method' => $request->method(),
    'http_path' => $request->path(),
    'ip' => $request->ip(),
    'content_id' => $content->id,
    'user_id' => empty($user) ? null : $user->id
]);
$visit->save();

return [
    'content' => App\Content::where('seo_slug', \Route::input('slug'))->first()
];