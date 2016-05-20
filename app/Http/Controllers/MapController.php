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
            'layers' => \App\Layeritem::with('layer', 'group', 'layer.content', 'group.content', 'layer.projection')
                ->where('map_id', $map->id)
                ->orderBy('displayorder')
                ->get(),
            'layerswitcher' => [
                "closed" => []
            ],
            'proxy' => url('proxy')
        ];
        
        return response()->json($config);
    }
    
    /**
     * Forward HTTP request
     */
    public function proxyRequest()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $url = base64_decode(\Input::get('url'));
        
        // Return not-allowed if is no OWS request
        if (
            !strpos($url, strtoupper('SERVICE')) 
            || !strpos($url, strtoupper('REQUEST'))
        ) {
            abort(400, 'Bad Request');
        }

        // Make request
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);

        $response = curl_exec($ch);
        list($header, $body) = explode("\r\n\r\n", $response, 2);

        // Send headers
        $i = 0;
        $headers = explode("\r\n", $header);
        foreach($headers as $header) {
            $i++;
            if ($i === 1) continue;
            if ($header === 'Transfer-Encoding: chunked') continue;
            header($header);
        }

        // Send file
        die($body);
    }

}
