<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class AdminController extends BaseController {

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
