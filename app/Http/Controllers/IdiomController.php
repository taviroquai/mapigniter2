<?php namespace App\Http\Controllers;

use App\Idiom;

/**
 * Handles idiom operations
 */
class IdiomController extends Controller
{    
    /**
     * Change session idiom
     * 
     * @param string $idiom
     * @return redirect
     */
    public function setIdiom($idiom)
    {
        Idiom::setIdiom($idiom);
        return redirect('/');
    }

}
