<?php

return [
    'user' => \Auth::user(),
    'brand' => App\Brand::where('active', 1)->first(),
    'contents' => App\Content::all()
];