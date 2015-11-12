<?php

namespace App;

class Layer extends Content
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'layers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'projection_id',
        'bing_key',
        'bing_imageryset',
        'mapquest_layer',
        'wms_servertype',
        'wms_url',
        'wms_layers',
        'wms_tiled',
        'wms_version',
        'wfs_url',
        'wfs_version',
        'wfs_typename',
        'feature_info_template',
        'gpx_filename',
        'kml_filename',
        'shapefile_filename',
        'shapefile_geomtype',
        'shapefile_wmsurl',
        'shapefile_msclass',
        'postgis_host',
        'postgis_port',
        'postgis_user',
        'postgis_pass',
        'postgis_dbname',
        'postgis_schema',
        'postgis_table',
        'postgis_field',
        'postgis_attributes',
        'geojson_geomtype',
        'geojson_attributes',
        'geojson_features',
        'search',
        'ol_style_static_icon',
        'ol_style_static_fill_color',
        'ol_style_static_stroke_color',
        'ol_style_static_stroke_width',
        'ol_style_field_icon',
        'ol_style_field_fill_color',
        'ol_style_field_stroke_color',
        'ol_style_field_stroke_width'
    ];
    
    /**
     * Maps relation
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function maps()
    {
        return $this->belongsToMany('App\Map');
    }
    
    public function content()
    {
        return $this->belongsTo('App\Content');
    }
    
    public function projection()
    {
        return $this->belongsTo('App\Projection', 'projection_id', 'srid');
    }
    
    static function bingImageryOptions()
    {
        return [
            'Road' => 'Road',
            'Aerial' => 'Aerial',
            'AerialWithLabels' => 'AerialWithLabels',
            'collinsBart' => 'collinsBart',
            'ordnanceSurvey' => 'ordnanceSurvey'
        ];
    }
    
    static function mapquestLayerOptions()
    {
        return [
            'sat' => 'Satellite'
        ];
    }
    
    static function typeOptions()
    {
        return [
            'group' => 'Group',
            'geojson' => 'Map Editor',
            'bing' => 'Bing',
            'mapquest' => 'MapQuest',
            'osm' => 'Open Street Map',
            'opencyclemap' => 'Open Cycle Map',
            'wms' => 'WMS',
            'wfs' => 'WFS',
            'gpx' => 'GPX',
            'kml' => 'KML',
            'shapefile' => 'Shapefile (requires MapServer)',
            'postgis' => 'Postgis'
        ];
    }
    
    static function wmsServerTypeOptions()
    {
        return [
            'mapserver' => 'MapServer',
            'geoserver' => 'GeoServer'
        ];
    }
    
    static function wmsTiledOptions()
    {
        return [
            '0' => 'FALSE',
            '1' => 'TRUE'
        ];
    }
    
    static function wmsVersionOptions()
    {
        return [
            '1.1.0' => '1.1.0'
        ];
    }
    
    /**
     * Save style icon image
     * 
     * @param null|File $file
     */
    public function saveStyleIcon($file)
    {
        if ($file) {
            $filename = 'ol_style_static_icon.'.$file->getClientOriginalExtension();
            $file->move($this->getPublicStoragePath(), $filename);
            $this->ol_style_static_icon = $filename;
            $this->save();
        }
    }
    
    /**
     * Save gpx file
     * 
     * @param null|File $file
     */
    public function saveGPXFile($file)
    {
        if ($file) {
            $filename = 'gpxfile.'.$file->getClientOriginalExtension();
            $file->move($this->getPublicStoragePath(), $filename);
            $this->gpx_filename = $filename;
            $this->save();
        }
    }
    
    /**
     * Save kml file
     * 
     * @param null|File $file
     */
    public function saveKMLFile($file)
    {
        if ($file) {
            $filename = 'kmlfile.'.$file->getClientOriginalExtension();
            $file->move($this->getPublicStoragePath(), $filename);
            $this->kml_filename = $filename;
            $this->save();
        }
    }
    
    /**
     * Save shapefile
     * 
     * @param null|File $file
     */
    public function saveShapeFile($file)
    {
        if ($file) {
            $filename = 'shapefile.'.$file->getClientOriginalExtension();
            $file->move($this->getPublicStoragePath(), $filename);
            $this->shapefile_filename = $filename;
            $this->save();
            
            // Unpack
            $zip = new \ZipArchive;
            $res = $zip->open($this->getPublicStoragePath() . '/' . $filename);
            if ($res === TRUE) {
                $zip->extractTo($this->getPublicStoragePath());
                $zip->close();
                $files = glob($this->getPublicStoragePath() . '/*.shp');
                if (!empty($files)) {
                    $this->shapefile_filename = basename($files[0]);
                    $this->save();
                }
            }
        }
        if ($this->shapefile_filename) {
            $this->shapefile_wmsurl = "http://{$_SERVER['SERVER_NAME']}/cgi-bin/mapserv?map=" . storage_path('layer/' . $this->id . '/mapfile.map') . "&";
            $this->save();
            $this->generateMapfile();
        }
    }
    
    

    /**
     * Save postgis file
     * 
     * @return boolean
     */
    public function savePostgisFile()
    {
        // Make connection
        $dsn = "pgsql:host={$this->postgis_host};port={$this->postgis_port};dbname={$this->postgis_dbname}";
        $pdo = new \PDO($dsn, $this->postgis_user, $this->postgis_pass);
        
        // Get table CRS
        $stm = $pdo->query("SELECT Find_SRID('{$this->postgis_schema}','{$this->postgis_table}', '{$this->postgis_field}') as srid");
        if (!$stm) throw new \Exception ('Could not execute query');
        $stm->execute();
        $srid = $stm->fetchColumn(0);
        
        // Get items
        $sql = "SELECT {$this->postgis_attributes}, ST_AsGeoJSON({$this->postgis_field}) as json FROM {$this->postgis_schema}.{$this->postgis_table}";
        $stm = $pdo->query($sql);
        if (!$stm) throw new \Exception ('Could not execute query');
        $stm->execute();
        $items = $stm->fetchAll(\PDO::FETCH_OBJ);
        
        $json = [
            'type' => 'FeatureCollection',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'EPSG:' . $srid
                ]
            ],
            'features' => []
        ];

        foreach($items as $item) {
            $feature = ['type' => 'Feature', 'geometry' => null, 'properties' => null];
            $feature['geometry'] = json_decode($item->json);
            unset($item->json);
            unset($item->the_geom);
            $feature['properties'] = $item;
            $json['features'][] = $feature;
        }
        
        // Create filename
        if (!is_dir($this->getPublicStoragePath())) {
            mkdir($this->getPublicStoragePath(), 0777, true);
        }
        $filename = $this->getPublicStoragePath() . '/postgis.json';
        file_put_contents($filename, json_encode($json));
    }
    
    /**
     * Save gpx file
     */
    public function saveGeoJSONFile()
    {
        if (empty($this->geojson_features)) {
            $this->geojson_features = '{"type":"FeatureCollection","features":[]}';
        }
        if (!is_dir($this->getPublicStoragePath())) {
            mkdir($this->getPublicStoragePath(), 0777, true);
        }
        $filename = $this->getPublicStoragePath() . '/geojson.json';
        @file_put_contents($filename, $this->geojson_features);
    }
    
    /**
     * Get public storage path
     * 
     * @return string
     */
    public function getPublicStoragePath()
    {
        return public_path('storage/layer/'.$this->id);
    }
    
    /**
     * Get icons storage path
     * 
     * @return string
     */
    public function getIconsPath()
    {
        return 'storage/layer/'.$this->id.'/icons';
    }
    
    /**
     * Get all icons images
     * 
     * @return array
     */
    public function getIconsImages()
    {
        $items = glob(public_path($this->getIconsPath()).'/*.{jpg,png,gif}', GLOB_BRACE);
        return $items;
    }
    
    /**
     * Get icon url
     * 
     * @param string $image
     * @return string
     */
    public function getIconImageUrl($image)
    {
        return asset($this->getIconsPath()).'/'.basename($image);
    }
    
    /**
     * Delete layer
     */
    public function delete() {
        //array_map('unlink', glob($this->getPublicStoragePath()."/*"));
        parent::delete();
    }
    
    /**
     * Generate mapfile
     */
    protected function generateMapfile()
    {
        if (!is_dir(storage_path('layer/'.$this->id))) {
            mkdir(storage_path('layer/'.$this->id), 0777, true);
        }
        ob_start();
        include storage_path('app/template.map');
        $content = ob_get_clean();
        @file_put_contents(storage_path('layer/'.$this->id) . '/mapfile.map', $content);
    }

    /**
     * Convert hexadecimal color to rgb
     * 
     * @param string $hex
     * @return string
     */
    protected function hex2rgb($hex)
    {
        if (strpos($hex, '#') === 0) {
            $hex = str_replace("#", "", $hex);

            if(strlen($hex) == 3) {
                $r = hexdec(substr($hex,0,1).substr($hex,0,1));
                $g = hexdec(substr($hex,1,1).substr($hex,1,1));
                $b = hexdec(substr($hex,2,1).substr($hex,2,1));
            } else {
                $r = hexdec(substr($hex,0,2));
                $g = hexdec(substr($hex,2,2));
                $b = hexdec(substr($hex,4,2));
            }
            $rgb = array($r, $g, $b);
            return implode(" ", $rgb); // returns the rgb values separated by space
        }
        return $hex;
    }
    
    /**
     * Get icon path
     * 
     * @return string
     */
    protected function getStaticIconPath()
    {
        return public_path('storage/layer/' . $this->id . '/' . $this->ol_style_static_icon);
    }
    
    /**
     * Get icon size
     * 
     * @return int
     */
    protected function getStaticIconSize()
    {
        list($width, $height) = getimagesize($this->getStaticIconPath());
        return $height;
    }
    
    /**
     * Convert shapefile name to mapfile layer data
     * 
     * @return string
     */
    protected function getMapfileData()
    {
        return substr($this->shapefile_filename, 0, strrpos($this->shapefile_filename, '.'));
    }
    
}
