<?php

return [
    'locations' => App\Location::with('content')->get()
];