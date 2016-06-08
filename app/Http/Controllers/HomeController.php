<?php namespace App\Http\Controllers;

use Route;
use Schema;
use App\Page;
use App\Idiom;

class HomeController extends Controller {

	/*
	|--------------------------------------------------------------------------
	| Welcome Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders the "marketing page" for the application and
	| is configured to only allow guests. Like most of the other sample
	| controllers, you are free to modify or remove it as you desire.
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('guest');
	}

	/**
	 * Show the application welcome screen to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		return view('home');
	}
    
    /**
     * Load user pages routes
     */
    static function loadUserPagesRoutes()
    {
        // Check database connectivity
        try {
            Schema::hasTable('pages');
        } catch (Exception $ex) {
            abort(503, 'Service Unavailable: Database');
        }
        
        // Load user pages
        Route::group(['middleware' => ['visit']], function () {
            
            

            // Run all pages
            if (Schema::hasTable('pages')) {
                foreach(Page::where('active', 1)->get() as $page) {

                    // Create page route
                    Route::get($page->route, function () use ($page) {
                        
                        // Load idiom
                        Idiom::loadIdiom();

                        // Get page data file
                        $data = (array) include($page->getDataPath());

                        // Display page view file
                        return view($page->getView(), $data);
                    });
                }
            }
        });
    }

}
