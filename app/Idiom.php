<?php

namespace App;

use App;
use Session;

/**
 * Implements session idioms operations
 */
class Idiom
{
    
    /**
     * Change session idiom
     * 
     * @param string $idiom
     * @return redirect
     */
    static function setIdiom($idiom)
    {
        App::setLocale($idiom);
        Session::put('idiom', App::getLocale());
    }
    
    /**
     * Load idiom
     */
    static function loadIdiom()
    {
        if (!(Session::has('idiom'))) {
            Session::put('idiom', App::getLocale());
        }
        App::setLocale(Session::get('idiom'));
    }
    
    /**
     * Get available idioms
     */
    static function getAvailableIdioms()
    {
        $items = glob(app_path('../resources/lang').'/*', GLOB_ONLYDIR);
        foreach($items as &$item) {
            $item = basename($item);
        }
        return $items;
    }
}
