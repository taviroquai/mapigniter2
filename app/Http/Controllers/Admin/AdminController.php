<?php namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use App\Idiom;

class AdminController extends BaseController {
    
    /**
     * Init admin controller
     */
    public function __construct() {
        Idiom::loadIdiom();
    }

    /**
	 * Show backoffice
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('admin/dashboard');
	}

}
