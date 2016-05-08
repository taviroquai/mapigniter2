<?php

namespace App;

use CrEOF\Geo\WKB\Parser as WKBParser;

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
        'geopackage_filename',
        'geopackage_table',
        'geopackage_field',
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
            'postgis' => 'Postgis',
            'geopackage' => 'GeoPackage (requires PHP SQLite extension)'
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
            '1.0.0' => '1.0.0',
            '1.1.0' => '1.1.0',
            '1.3.0' => '1.3.0'
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
        // Create valid geojson string
        if (empty($this->geojson_features)) {
            $this->geojson_features = '{"type":"FeatureCollection","features":[]}';
        }
        
        // Fix missing CRS
        if (!strstr($this->geojson_features, '"name":"EPSG:')) {
            $this->geojson_features = substr($this->geojson_features, 0, strlen($this->geojson_features)-1);
            $this->geojson_features .= ',"crs":{"type":"name","properties":{"name":"EPSG:' . $this->projection_id . '"}}' . '}';
        }
        
        // Fix missing layer directory
        if (!is_dir($this->getPublicStoragePath())) {
            mkdir($this->getPublicStoragePath(), 0777, true);
        }
        
        // Save file
        $filename = $this->getPublicStoragePath() . '/geojson.json';
        @file_put_contents($filename, $this->geojson_features);
    }
    
    /**
     * Save geopackage file
     * 
     * @param null|File $file
     */
    public function saveGeoPackageFile($file)
    {
        // Set PHP settings
        ini_set('memory_limit','512M');
        
        // Save uploaded file
        if ($file) {
            $filename = 'geopackage.'.$file->getClientOriginalExtension();
            $file->move($this->getPublicStoragePath(), $filename);
            $this->geopackage_filename = $filename;
            $this->save();
        }
        
        // Make connection
        $dsn = "sqlite:" . $this->getPublicStoragePath() . '/' . $this->geopackage_filename;
        $pdo = new \PDO($dsn);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        
        // Get table feature id
        try {
            $stm = $pdo->query("SELECT column_name FROM gpkg_data_columns WHERE table_name = '{$this->geopackage_table}' and title = 'FeatureID'");
            if (!$stm) throw new \Exception ('Could not execute query');
            $stm->execute();
            $column_id = $stm->fetchColumn(0);
            $column_id = empty($column_id) ? 'id' : $column_id;
        } catch (\PDOException $e) {
            $column_id = false;
        }
        
        // Get table srid
        $stm = $pdo->query("SELECT srs_id FROM gpkg_contents WHERE table_name = '{$this->geopackage_table}'");
        if (!$stm) throw new \Exception ('Could not execute query');
        $stm->execute();
        $srid = $stm->fetchColumn(0);
        
        // Get table items
        $stm = $pdo->query("SELECT * FROM {$this->geopackage_table} WHERE {$this->geopackage_field} IS NOT NULL");
        if (!$stm) throw new \Exception ('Could not execute query');
        $stm->execute();
        $items = $stm->fetchAll(\PDO::FETCH_OBJ);
        
        // Create directories
        if (!is_dir($this->getPublicStoragePath())) {
            mkdir($this->getPublicStoragePath(), 0777, true);
        }
        if (!is_dir($this->getPublicStoragePath().'/bin')) {
            mkdir($this->getPublicStoragePath().'/bin', 0777, true);
        }
        
        // Create JSON from GeoPackage table if does not exists
        $filename = $this->getPublicStoragePath() . '/geopackage.json';
        if (!file_exists($filename)) {
            
            // Get WKB parser
            $parser = new WKBParser();
            
            // Init JSON string
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

            $id = 1;
            foreach($items as $item) {

                // Create stream from binary geometry
                $featname = $this->getPublicStoragePath().'/bin/'.($column_id ? $item->{$column_id} : $id);
                file_put_contents($featname, $item->{$this->geopackage_field});

                // Create feature
                $feature = ['type' => 'Feature', 'geometry' => null, 'properties' => null];
                list($header, $wkb) = $this->parseGeoPackageGeometry($featname);
                //$feature['geometry'] = $parser->parse(bin2hex($wkb));
                $feature['geometry'] = bin2hex($wkb);
                unset($item->{$this->geopackage_field}); // Remove from feature attributes
                $feature['properties'] = $item;
                $json['features'][] = $feature;
                $id++;
            }

            // Save JSON table
            file_put_contents($filename, json_encode($json, JSON_PRETTY_PRINT));
        }
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
     * Check if color is rgba format
     * @param type $color
     * @return type
     */
    protected function parseRGBA($color)
    {
        $expr = '/rgba\(([0-9]+)\,([0-9]+)\,([0-9]+)\,([0-9]*\.[0-9]+)\)/';
        if (preg_match($expr, $color, $m)) {
            return $m;
        }
        return false;
    }
    
    /**
     * Get opacity value
     * 
     * @param array $parsedRGBA
     * @return int
     */
    protected function getOpacity($parsedRGBA)
    {
        return (int) ($parsedRGBA[4] * 100);
    }
    
    /**
     * Format parsed color to RGB
     * 
     * @param array $parsedRGBA
     * @return string
     */
    protected function getRGB($parsedRGBA)
    {
        return $parsedRGBA[1].' '.$parsedRGBA[2].' '.$parsedRGBA[3];
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
    
    /**
     * Parse GeoPackageBinaryHeader
     * 
     * References
     * 
     * http://www.geopackage.org/spec/#gpb_spec
     * 
     * https://en.wikipedia.org/wiki/Well-known_text
     * http://php.net/manual/en/function.unpack.php
     * http://ngageoint.github.io/geopackage-js/ (NodeJS + SQL.js Demo)
     * 
     * @param string $filename
     */
    protected function parseGeoPackageGeometry($filename)
    {
        // Default values
        $header = [
            'magic'     => '',
            'version'   => 0,
            'flags'     => 0,
            'srs_id'    => 0,
            'envelope'  => []
        ];
        $wkb = '';
        
        // Open binary
        $h = fopen($filename, 'rb');
        if (!$h) {
            throw new \Exception('Could not open stream (geometry data)');
        }
            
        // Get stream stats
        $fstat = fstat($h);
        $total = $fstat['size'];
        $read = 0;

        // Parse header
        $bytes = unpack('A2magic/c1version/c1flags', fread($h, 4));
        $read += 4;
        $header['magic'] = $bytes['magic'];
        $header['version'] = $bytes['version'];
        $header['flags'] = $bytes['flags'];
        $header['envelop_flag'] = ($header['flags'] >> 1) & 7;
        $header['byte_order'] = $header['flags'] & 1;

        // Parse SRID
        $unpack_op = $header['byte_order'] ? 'V' : 'N';
        $bytes = array_values(unpack($unpack_op, fread($h, 4)));
        $read += 4;
        $header['srs_id'] = $bytes[0];

        switch ($header['envelop_flag']) {
        case 1: // 32 bytes envelop
            $data = fread($h, 32);
            $data = $header['byte_order'] ? strrev($data) : $data;
            $unpack_op = $header['byte_order'] ? 'd*' : 'd*';
            $bytes = array_values(unpack($unpack_op, $data));
            $header['envelope'] = [
                'minx' => $bytes[0],
                'miny' => $bytes[1],
                'maxx' => $bytes[2],
                'maxy' => $bytes[3],
                'minz' => false,
                'maxz' => false,
                'minm' => false,
                'maxm' => false
            ];
            $read += 32;
            break;
        case 2: // 48 bytes envelop
            $data = fread($h, 48);
            $data = $header['byte_order'] ? strrev($data) : $data;
            $unpack_op = $header['byte_order'] ? 'd*' : 'd*';
            $bytes = array_values(unpack($unpack_op, $data));
            $header['envelope'] = [
                'minx' => $bytes[0],
                'miny' => $bytes[1],
                'maxx' => $bytes[2],
                'maxy' => $bytes[3],
                'minz' => $bytes[4],
                'maxz' => $bytes[5],
                'minm' => false,
                'maxm' => false
            ];
            $read += 48;
            break;
        case 3: // 48 bytes envelop
            $data = fread($h, 48);
            $data = $header['byte_order'] ? strrev($data) : $data;
            $unpack_op = $header['byte_order'] ? 'd*' : 'd*';
            $bytes = array_values(unpack($unpack_op, $data));
            $header['envelope'] = [
                'minx' => $bytes[0],
                'miny' => $bytes[1],
                'maxx' => $bytes[2],
                'maxy' => $bytes[3],
                'minz' => false,
                'maxz' => false,
                'minm' => $bytes[4],
                'maxm' => $bytes[5]
            ];
            $read += 48;
            break;
        default: ;// 0 envelop
        }

        // Get WKB from bytes left
        $wkb = fread($h, $total - $read);

        // Close handler
        fclose($h);
        
        return [$header, $wkb];
    }
    
}
