<?php namespace App\Http\Controllers\Admin;

use App\Content;
use App\Layer;
use App\GeoPackage;

class LayerController extends AdminController
{
    /**
     * Get content index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/layers');
	}
    
    /**
     * Edit content
     * 
     * @param Layer $layer
     * @return \Illuminate\View\View
     */
    public function form(Layer $layer)
	{
        if (!$layer->isUserAllowed(\Auth::user())) {
            return response(view('admin/unauthorized'), 401);
        }
        
		return view('admin/layers-edit', compact('layer', 'extent', 'center'));
	}
    
    /**
     * Edit content ownership
     * 
     * @param Layer $layer
     * @return \Illuminate\View\View
     */
    public function formOwnership(Layer $layer)
	{
		return view('admin/layers-ownership', compact('layer'));
	}
    
    /**
     * Save map
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        // Set php options
        set_time_limit(60 * 10); // 10 minutes
        
        // Get input
        $input = \Input::except('_token');
        $rules = [];
        
        // Validate map content
        if (empty($input['id'])) {
            
            // Pre validate
            $input['seo_slug'] = empty($input['seo_slug']) ? str_slug($input['title']) : $input['seo_slug'];
            
            // Validate required params
            $rules = [
                'title' => 'required|max:255',
                'seo_slug' => 'unique:contents'.(!empty($input['id']) ? ',seo_slug,'.$input['id'] : '')
            ];
        }
        
        // Validate required params
        switch($input['type']) {
            case 'bing':
                $rules['bing_key'] = 'required';
                break;
            case 'wms':
                $rules['wms_url'] = 'required';
                $rules['wms_layers'] = 'required';
                break;
            case 'wfs':
                $rules['wfs_url'] = 'required';
                $rules['wfs_typename'] = 'required';
                break;
            case 'gpx':
                if (empty($input['id'])) {
                    $rules['gpx_filename_0'] = 'required';
                }
                break;
            case 'kml':
                if (empty($input['id'])) {
                    $rules['kml_filename_0'] = 'required';
                }
                break;
            case 'postgis':
                $rules['postgis_host'] = 'required';
                $rules['postgis_port'] = 'required';
                $rules['postgis_user'] = 'required';
                $rules['postgis_schema'] = 'required';
                $rules['postgis_table'] = 'required';
                $rules['postgis_field'] = 'required';
                $input['postgis_attributes'] = empty($input['postgis_attributes']) ? '*' : $input['postgis_attributes'];
                break;
            case 'geojson':
                $rules['geojson_attributes'] = 'required';
                break;
            case 'shapefile':
                if (empty($input['id'])) {
                    $rules['shapefile_filename_0'] = 'required';
                }
                break;
            case 'geopackage':
                $rules['geopackage_table'] = 'required';
                $rules['geopackage_fields'] = 'required';
                $input['geopackage_fields'] = implode(',', $input['geopackage_fields']);
                break;
            case 'group':
            default:;
            
        }
        
        // When validation fails
        $validator = \Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Load layer
        if (empty($input['id'])) {
            $content = new Content;
            $content->user_id = \Auth::user()->id;
            $content->fill($input);
            $content->save();
            $layer = new Layer;
            $layer->user_id = \Auth::user()->id;
            $layer->content()->associate($content);
        } else {
            $layer = Layer::find($input['id']);
        }
        
        // Validate permission
        if (!$layer->isUserAllowed(\Auth::user())) {
            return response('', 401)->json(['success' => false]);
        }
        
        // Save changes
        $layer->fill($input);
        $layer->save();
        
        // Process files
        $layer->saveStyleIcon(\Request::file('ol_style_static_icon_0'));
        $layer->saveGPXFile(\Request::file('gpx_filename_0'));
        $layer->saveKMLFile(\Request::file('kml_filename_0'));
        $layer->saveShapeFile(\Request::file('shapefile_filename_0'));
        if ($layer->type === 'geojson') {
            $layer->saveGeoJSONFile();
        }
        if ($layer->type === 'geopackage') {
            try {
                $layer->saveGeoPackageFile();
            } catch (\PDOException $e) {
                return response()->json(['errors' => ['geopackage_filename_0_error' => [$e->getMessage()]]]);
            } catch (\Exception $e) {
                return response()->json(['errors' => ['geopackage_filename_0_error' => [$e->getMessage()]]]);
            }
        }
        
        if ($layer->type === 'postgis') {
            try {
                $layer->savePostgisFile();
            } catch (\PDOException $e) {
                return response()->json(['errors' => ['postgis_error' => [$e->getMessage()]]]);
            } catch (\Exception $e) {
                return response()->json(['errors' => ['postgis_error' => [$e->getMessage()]]]);
            }
        }
        
        // Response with redirect
        $redirect = url('/admin/layers/list');
        if (!isset($input['close'])) {
            $redirect = url('/admin/layers/form/' . $layer->id);
            if (empty($input['id'])) {
                $redirect .= '#features';
            }
        }
        return response()->json(['success' => 'Layer saved', 'redirect' => $redirect]);
    }
    
    /**
     * Get all layers
     * 
     * @return \Illiminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Layer::with('content')->get()]);
	}
    
    /**
     * Delete layer
     * 
     * @param Layer $layer
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Layer $layer)
    {
        $layer->delete();
        return redirect('admin/layers/list');
    }
    
    /**
     * Upload icons
     * 
     * @param Layer $layer
     * @return \Illiminate\Http\JsonResponse
     */
    public function upload(Layer $layer)
    {
        // Process seo image
        $files = (array) \Request::file('image_uploader');
        foreach($files as $file) {
            $filename = $file->getClientOriginalName();
            $file->move(public_path($layer->getIconsPath()), $filename);
        }
        
        // Response
        return response()->json(['success' => true]);
    }
    
    /**
     * Delete icon
     * 
     * @param Layer $layer
     * @return \Illiminate\Http\JsonResponse
     */
    public function deleteIconImage(Layer $layer, $image)
    {
        $result = unlink($layer->getIconsPath().'/'.$image);
        return response()->json(['success' => $result]);
    }
    
    /**
     * Import CSV (only geojson layer)
     */
    public function importCSV(Layer $layer)
    {
        $filename = public_path('storage/layer/2/geojson.json');
        $layer->geojson_features = file_get_contents($filename);
        
        // Get attributes and indexes
        $attributes = explode(',', $layer->geojson_attributes);
        $csv_attributes = [];
        $x_index = 1;
        $y_index = 2;
        
        // Get current features
        $json = json_decode($layer->geojson_features);
        $json->features = [];
        
        // Parse csv file
        $file = \Request::file('csv_uploader');
        $filename = $file->getClientOriginalName();
        $file->move(public_path($layer->getIconsPath().'/'), $filename);
        $row = 1;
        $tmp = public_path($layer->getIconsPath()).'/'.$filename;
        if (($handle = fopen($tmp, "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $num = count($data);
                
                // Get csv columns on first row
                if ($row === 1) {
                    for ($c = 0; $c < $num; $c++) {
                        
                        //validate column
                        if (in_array($data[$c], $attributes)) {
                            $csv_attributes[] = $data[$c];
                        }
                        
                        // Validate x column
                        if (strtolower($data[$c]) == 'x') {
                            $x_index = $c;
                        }
                        
                        // Validate y column
                        if (strtolower($data[$c]) == 'y') {
                            $y_index = $c;
                        }
                    }
                } else {
                    
                    // Create feature
                    $feature = new \stdClass();
                    $feature->type = 'Feature';
                    $feature->properties = new \stdClass();
                    $feature->geometry = new \stdClass();
                    $feature->geometry->type = 'Point';
                    $feature->geometry->coordinates = [];

                    for ($c=0; $c < $num; $c++) {
                        
                        // Add attribute value
                        if (isset($csv_attributes[$c]) && in_array($csv_attributes[$c], $attributes)) {
                            $feature->properties->{$csv_attributes[$c]} = $data[$c];
                        }
                        
                        // Add x value
                        if ($c == $x_index) {
                            $feature->geometry->coordinates[] = (float) $data[$c];
                        }
                        
                        // Add y value
                        if ($c == $y_index) {
                            $feature->geometry->coordinates[] = (float) $data[$c];
                        }
                    }
                    $json->features[] = $feature;
                }
                
                // Next row
                $row++;
            }
            
            // Clear csv file
            fclose($handle);
            unlink($tmp);

            // Update layer
            $layer->geojson_features = json_encode($json);
            $layer->save();
            $layer->saveGeoJSONFile();
            
            //Response
            return response()->json(['success' => true]);
        }
        
        //Response
        return response()->json(['success' => false]);
    }
    
    /**
     * Get GeoPackage info
     * 
     * @return \Illiminate\Http\JsonResponse
     */
    public function getGeoPackageInfo()
    {
        try {
            
            // Process geopackage upload
            $file = \Request::file('geopackage_filename');
            $filename = $file->getClientOriginalName();
            $file->move(public_path(\Auth::user()->getStoragePath()), $filename);
        
            // Make connection
            $filename = public_path(\Auth::user()->getStoragePath().'/'. $filename);
            $geopackage = new GeoPackage($filename);
            $geopackage->validate();

            // Response
            return response()->json(['success' => true, 'result' => $geopackage->getInfo()]);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
