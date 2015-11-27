<?php

return [
    'user' => \Auth::user(),
    'brand' => App\Brand::where('active', 1)->first(),
    'map' => App\Map::orderBy('id')->first()
];
