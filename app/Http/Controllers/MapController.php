<?php namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Map;

class MapController extends BaseController
{
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
