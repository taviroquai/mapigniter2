<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Idiom;
use App\Map;
use App\Brand;

class MapController extends BaseController
{
    /**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
        // Load idiom
        Idiom::loadIdiom();
	}
    
    /**
     * Load map layout
     * 
     * @param Map $map
     */
    public function getMap(Map $map)
    {
        $data = [
            'user' => \Auth::user(),
            'brand' => Brand::where('active', 1)->first(),
            'map' => $map
        ];
        return view('map', $data);
    }

    /**
     * Get map configuration
     *
     * @return Response
     */
    public function getConfig(Map $map)
    {
        // Build output JSON config
        $map->projection;
        $config = [
            'map' => $map,
            'layers' => \App\Layeritem::with('layer', 'group', 'layer.content', 'group.content')
                ->where('map_id', $map->id)
                ->orderBy('displayorder')
                ->get(),
            'layerswitcher' => [
                "closed" => []
            ],
        ];
        
        return response()->json($config);
    }

}
