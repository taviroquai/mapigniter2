<?php namespace App\Http\Controllers\Admin;

use Illuminate\Routing\Controller as BaseController;
use App\Content;
use App\Map;
use App\Layeritem;

class MapController extends BaseController
{
    /**
     * Get content index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/maps');
	}
    
    /**
     * Edit content
     * 
     * @param Map $map
     * @return \Illuminate\View\View
     */
    public function form(Map $map)
	{
        if (!$map->isUserAllowed(\Auth::user())) {
            return response(view('admin/unauthorized'), 401);
        }
        
        $center = empty($map->center) ? ['0', '0'] : explode(' ', $map->center);
        $map->zoom = empty($map->zoom) ? 9 : $map->zoom;
		return view('admin/maps-edit', compact('map', 'extent', 'center'));
	}
    
    /**
     * Save map
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        
        // Validate map content
        if (empty($input['id'])) {
            
            // Pre validate
            $input['seo_slug'] = empty($input['seo_slug']) ? str_slug($input['title']) : $input['seo_slug'];
            
            // Create validator
            $validator = \Validator::make($input, [
                'title' => 'required|max:255',
                'seo_slug' => 'unique:contents'.(!empty($input['id']) ? ',seo_slug,'.$input['id'] : '')
            ]);
            
            // When validation fails
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->messages()]);
            }
        }
        
        // Load content
        if (empty($input['id'])) {
            $content = new Content;
            $content->user_id = \Auth::user()->id;
            $content->fill($input);
            $content->save();
            $map = new Map;
            $map->user_id = \Auth::user()->id;
            $map->content()->associate($content);
        } else {
            $map = Map::find($input['id']);
        }
        
        // Save changes
        $input['center'] = implode(' ', $input['center']);
        $map->fill($input);
        $map->save();
        
        // Response
        return response()->json(['success' => 'Map saved', 'redirect' => url('/admin/maps/list')]);
    }
    
    /**
     * Add layer item
     * 
     * @return \Redirect
     */
    public function addLayeritem()
    {
        $input = \Input::except('_token');
        $item = new Layeritem($input);
        $item->displayorder = Layeritem::count() + 1;
        $item->save();
        
        // Response
        return response()->json(['success' => 'Layer added', 'title' => $item->layer->content->title, 'id' => $item->id]);
    }
    
    /**
     * Delete layer item
     * 
     * @param Map $map
     * @param Layeritem $layeritem
     * @return \Redirect
     */
    public function delLayeritem(Map $map, Layeritem $layeritem)
    {
        $displayorder = $layeritem->displayorder;
        $layeritem->delete();
        Layeritem::where('displayorder', '>', $displayorder)->decrement('displayorder');
        
        // Response
        return response()->json(['success' => 'Layer deleted', 'map' => $map]);
    }
    
    /**
     * Order layer item up
     * 
     * @param Map $map
     * @param Layeritem $layeritem
     * @return \Redirect
     */
    public function orderupLayeritem(Map $map, Layeritem $layeritem)
    {
        $old = $layeritem->displayorder;
        Layeritem::where('displayorder', $old-1)->update(['displayorder' => $old]);
        $layeritem->displayorder--;
        $layeritem->save();
        
        // Response
        return response()->json(['success' => 'Layer deleted', 'map' => $map]);
    }
    
    /**
     * Order layer item down
     * 
     * @param Map $map
     * @param Layeritem $layeritem
     * @return \Redirect
     */
    public function orderdownLayeritem(Map $map, Layeritem $layeritem)
    {
        $old = $layeritem->displayorder;
        Layeritem::where('displayorder', $old+1)->update(['displayorder' => $old]);
        $layeritem->displayorder++;
        $layeritem->save();
        
        // Response
        return response()->json(['success' => 'Layer deleted', 'map' => $map]);
    }
    
    /**
     * Get all maps
     * 
     * @return \Illiminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Map::with('content')->get()]);
	}
    
    /**
     * Delete map
     * 
     * @param Map $map
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Map $map)
    {
        $map->delete();
        return redirect('admin/maps/list');
    }

}
