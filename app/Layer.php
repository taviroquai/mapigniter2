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
     * Save postgis file
     * 
     * @return boolean
     */
    public function savePostgisFile()
    {
        
        // Get table CRS
        $result = \DB::select(\DB::raw("SELECT Find_SRID('{$this->postgis_schema}','{$this->postgis_table}', '{$this->postgis_field}') as srid"));
        
        // Get items
        $sql = "SELECT {$this->postgis_attributes}, ST_AsGeoJSON({$this->postgis_field}) as json FROM {$this->postgis_schema}.{$this->postgis_table}";
        $items = \DB::select(\DB::raw($sql), []);
        
        $json = [
            'type' => 'FeatureCollection',
            'crs' => [
                'type' => 'name',
                'properties' => [
                    'name' => 'EPSG:' . $result[0]->srid
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
        @mkdir($this->getPublicStoragePath(), 0777, true);
        $filename = $this->getPublicStoragePath() . '/postgis.json';
        @file_put_contents($filename, json_encode($json));
    }
    
    /**
     * Save gpx file
     */
    public function saveGeoJSONFile()
    {
        if (empty($this->geojson_features)) {
            $this->geojson_features = '{"type":"FeatureCollection","features":[]}';
        }
        @mkdir($this->getPublicStoragePath(), 0777, true);
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
        array_map('unlink', glob($this->getPublicStoragePath()."/*"));
        parent::delete();
    }
    
}
