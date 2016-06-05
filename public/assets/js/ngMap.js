
angular.module('ngMap', [],
function ($compileProvider) {
    
    // configure new 'compile' directive by passing a directive
    // factory function. The factory function injects the '$compile'
    $compileProvider.directive('compile', function($compile) {
        
        // directive factory creates a link function
        return function(scope, element, attrs) {
            
            scope.$watch(
                function(scope) {
                    // watch the 'compile' expression for changes
                    return scope.$eval(attrs.compile);
                },
                function(value) {
                    // when the 'compile' expression changes
                    // assign it into the current DOM
                    element.html(value);

                    // compile the new DOM and link it to the current
                    // scope.
                    // NOTE: we only compile .childNodes so that
                    // we don't get into infinite loop compiling ourselves
                    $compile(element.contents())(scope);
                }
            );
        };
    });
});

angular.module('ngMap').service('ol', function () { return ol });
angular.module('ngMap').service('proj4', function () { return proj4 });
angular.module('ngMap').service('lunr', function () { return lunr });

angular.module('ngMap')
.service('ngMapBuilder', ['$http', 'ol', 'proj4', 'config',
function ($http, ol, proj4, c) {
    
    var config = false;
    var extent = [];
    var projection;
    var map;
    var buildStatus = 0;
    
    /**
     * Build openlayers map
     * 
     * @param {type} cb
     * @returns {undefined}
     */

    $http.get(c.baseURL + '/maps/' + c.mapId + '/config')
    .success(function (r) {

        config = r;

        // Parse configuration items
        config.map.extent = config.map.projection.extent.split(' ');
        config.map.center = config.map.center.split(' ');
        config.map.center = [parseFloat(config.map.center[0]), parseFloat(config.map.center[1])];
        angular.forEach(config.map.extent, function (item, i) {
            extent.push(parseFloat(item));
        });
        config.map.extent = extent;
	
        proj4.defs("EPSG:" + config.map.projection.srid, config.map.projection.proj4_params);

        projection = new ol.proj.Projection({
            code: 'EPSG:' + config.map.projection.srid,
            units: 'm'
        });
        ol.proj.addProjection(projection);

        // Create OpenLayers map with specific projection and extent
        map = new ol.Map({
            target: 'map',
            layers: [],
            view: new ol.View({
                projection: projection,
                extent: config.map.extent,
                center: config.map.center,
                maxZoom: 32,
                zoom: 1
            })
        });
        
        // Add layers
        addLayers();

        // Center map
        map.getView().setCenter(config.map.center);
        map.getView().setZoom(parseInt(config.map.zoom));

        buildStatus = 1;
    });
    
    /**
     * Find base layer group
     * 
     * @param {type} id
     * @returns {item|Boolean}
     */
    var getGroup = function (id) {
        var result = false;
        map.getLayers().forEach(function (item) {
            if ((item instanceof ol.layer.Group) && (''+item.get('content').id === id)) {
                result = item;
            }
        });
        return result;
    };
    
    /**
     * Add layers to map from configuration
     * 
     * @returns {undefined}
     */
    var addLayers = function () {
        
        var group, glayers = {};
        
        angular.forEach(config.layers, function (item, i) {
            var layer = false;
            switch (item.layer.type) {
            case "mapquest":
                layer = createLayerMapQuest(item);
                break;
            case "osm":
                layer = criarLayerOSM(item);
                break;
            case "opencyclemap":
                layer = createLayerOpenCycleMap(item);
                break;
            case "bing":
                layer = createLayerBing(item);
                break;
            case "wms":
                layer = createLayerWMS(item);
                break;
            case "wfs":
                layer = createLayerWFS(item);
                break;
            case "gpx":
                layer = createLayerGPX(item);
                break;
            case "kml":
                layer = createLayerKML(item);
                break;
            case "shapefile":
                layer = createLayerShapefile(item);
                break;
            case "geopackage":
                layer = createLayerGeoPackage(item);
                break;
            case "postgis":
                layer = createLayerPostgis(item);
                break;
            case "geojson":
                layer = createLayerGeoJSON(item);
                break;
            case "group":
                layer = new ol.layer.Group(item);
                break;
            default:
                console ? console.log('Layer type not suported:', item.layer.type) : false;
            }
            if (layer) {
                layer.set('id', item.id);
                layer.set('title', item.layer.title);
                layer.set('group', item.group);
                layer.set('baselayer', item.baselayer);
                layer.setVisible(item.visible);
                layer.set('content', item.layer.content);
                layer.set('template', item.layer.feature_info_template !== '' ? item.layer.feature_info_template : false);
                layer.set('search', item.layer.search ? item.layer.search.split(',') : false);
                if (layer.get('group')) {
                    if (typeof glayers[layer.get('group').content.id] === 'undefined') {
                        glayers[layer.get('group').content.id] = new ol.Collection();
                    }
                    glayers[layer.get('group').content.id].push(layer);
                } else {
                    map.addLayer(layer);
                }
            }
        });
        
        // Add layers to groups
        for (var k in glayers) {
            group = getGroup(k);
            if (group) {
                group.setLayers(glayers[k]);
            }
        }
    };
    
    /**
     * Create legend URL
     * 
     * @param {string} url
     * @param {string} layer
     * @param {string} srs
     * @returns {String}
     */
    var createLegendUrl = function (url, layer, srs)
    {
        var finalurl = url;
        finalurl += finalurl.indexOf('?') === -1 ? '?' : ''; 
        return finalurl + '&' + ([
            'SERVICE=WMS',
            'VERSION=1.1.1',
            'REQUEST=GetLegendGraphic',
            'FORMAT=image%2Fpng',
            'SRS=EPSG:' + srs,
            'CRS=EPSG:' + srs,
            'LAYER=' + layer
        ].join('&'));
    };
    
    var addLayerProjection = function (layer)
    {
        // Add projection
        var lproj;
        if (layer.projection_id !== config.map.projection.srid) {
            proj4.defs("EPSG:" + layer.projection.srid, layer.projection.proj4_params);
            lproj = new ol.proj.Projection({
                code: 'EPSG:' + layer.projection.srid,
                units: 'm',
                extent: layer.projection.extent
            });
            ol.proj.addProjection(lproj);
        }
    };
    
    /**
     * Create map quest layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Tile}
     */
    var createLayerMapQuest = function (item)
    {
        var layer = new ol.layer.Tile({
            source: new ol.source.MapQuest({layer: 'sat'})
        });
        return layer;
    };
    
    /**
     * Create Bing layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Tile}
     */
    var createLayerBing = function (item) {
        item.layer['key'] = item.layer.bing_key;
        item.layer['imagerySet'] = item.layer.bing_imageryset;
        var layer = new ol.layer.Tile({
            visible: item.visible,
            source: new ol.source.BingMaps(item.layer)
        });
        return layer;
    };
    
    /**
     * Create OSM layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Tile}
     */
    var criarLayerOSM = function (item) {
        var layer = new ol.layer.Tile({
            source: new ol.source.OSM()
        });
        return layer;
    };
    
    /**
     * Create OpenCycle layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Tile}
     */
    var createLayerOpenCycleMap = function (item) {
        var layer = new ol.layer.Tile({
            source: new ol.source.OSM({
                attributions: [
                    new ol.Attribution({
                        html: 'All maps &copy; ' +
                        '<a href="http://www.opencyclemap.org/">OpenCycleMap</a>'
                    }),
                    ol.source.OSM.ATTRIBUTION
                ],
                url: 'http://{a-c}.tile.opencyclemap.org/cycle/{z}/{x}/{y}.png'
            })
        });
        return layer;
    };
    
    /**
     * Create WMS layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Tile}
     */
    var createLayerWMS = function (item) {
        
        item.layer['serverType'] = item.layer.wms_servertype;
        item.layer['url'] = item.layer.wms_url;
        item.layer['params'] = {
            'LAYERS': item.layer.wms_layers,
            'TILED': item.layer.wms_tiled,
            'VERSION': item.layer.wms_version,
            'SRS': 'EPSG:' + config.map.projection.srid,
            'CRS': 'EPSG:' + config.map.projection.srid
        };
        var layer = new ol.layer.Tile({
            visible: item.visible,
            source: new ol.source.TileWMS(item.layer)
        });
        layer.set('legendURL', createLegendUrl(item.layer['url'], item.layer.wms_layers, config.map.projection.srid));
        return layer;
    };
    
    /**
     * Create WFS layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Vector}
     */
    var createLayerWFS = function (item) {
        
        var finalurl, features, style;
        var gmlformat = (item.layer.wfs_version === '1.0.0' ? new ol.format.GML2 : new ol.format.GML3);
        var format = new ol.format.WFS({
            'gmlFormat': gmlformat
        });
        finalurl = item.layer.wfs_url + (item.layer.wfs_url.indexOf('?') > -1 ? '' : '?');
        
        // Add projection
        addLayerProjection(item.layer);
        
        function loadFeatures(url) {
            $http.post(config.proxy, {url: btoa(url)})
            .success(function (response) {                
                features = format.readFeatures(response, {
                    dataProjection: 'EPSG:' + item.layer.projection.srid,
                    featureProjection: 'EPSG:' + config.map.projection.srid
                });
                angular.forEach(features, function (f, i) {
                    source.addFeature(f);
                });
            });
        }

        // Carregador de source
        var params = [
            'SERVICE=WFS',
            'VERSION=' + item.layer.wfs_version,
            'REQUEST=GetFeature',
            'typename=' + item.layer.wfs_typename,
            'srsname=EPSG:' + config.map.projection.srid
        ];
        if (typeof item.layer.zoom_attribute !== 'undefined') {
            var source = new ol.source.Vector({
                features: [] 
            });
            finalurl =  finalurl + '&' + params.join('&') 
                + '&FILTER=' + encodeURIComponent('<Filter><PropertyIsLessThanOrEqualTo><PropertyName>' + item.layer.zoom_attribute + '</PropertyName><Literal>' + map.getView().getZoom() + '</Literal></PropertyIsLessThanOrEqualTo></Filter>');
        } else {
            var source = new ol.source.Vector({
                strategy: ol.loadingstrategy.bbox,
                loader: function (extent, resolution, projection) {
                    finalurl = finalurl + '&' + params.join('&')
                         + '&BBOX=' + extent.join(',') + ',EPSG:' + config.map.projection.srid;
                    loadFeatures(finalurl);
                }
            });
        }

        map.on('moveend', function () {
            if (typeof item.layer.zoom_attribute !== 'undefined') {
                finalurl = finalurl + '&FILTER=' + encodeURIComponent('<Filter><PropertyIsLessThanOrEqualTo><PropertyName>' + item.layer.zoom_attribute + '</PropertyName><Literal>' + map.getView().getZoom() + '</Literal></PropertyIsLessThanOrEqualTo></Filter>');
                source.clear();
                loadFeatures(finalurl);
            }
        });

        var layer = new ol.layer.Vector({
            visible: item.visible,
            source: source,
            style: function (feature, resolution) {
                style = createStyle(item.layer, feature, resolution);
                return [style];
            }
        });
        return layer;
    };
    
    /**
     * Create GPX layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Vector}
     */
    var createLayerGPX = function (item) {
        var style;
        var layer = new ol.layer.Vector({
            visible: item.visible,
            source: new ol.source.Vector({
                url: c.baseURL + '/storage/layer/' + item.layer.id + '/' + item.layer.gpx_filename,
                format: new ol.format.GPX()
            }),
            style: function (feature, resolution) {
                style = createStyle(item.layer, feature, resolution);
                return [style];
            }
        });
        return layer;
    };
    
    /**
     * Create KML layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Vector}
     */
    var createLayerKML = function (item) {
        
        var layer = new ol.layer.Vector({
            source: new ol.source.Vector({
                url: c.baseURL + '/storage/layer/' + item.layer.id + '/' + item.layer.kml_filename,
                format: new ol.format.KML()
            })
        });
        return layer;
    };
    
    /**
     * Create Shapefile layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Tile}
     */
    var createLayerShapefile = function (item) {
        
        item.layer['serverType'] = 'mapserver';
        item.layer['url'] = item.layer.shapefile_wmsurl;
        item.layer['params'] = {
            'LAYERS': item.layer.content.seo_slug,
            'TILED': false,
            'VERSION': '1.1.0',
            'SRS': 'EPSG:' + config.map.projection.srid,
            'CRS': 'EPSG:' + config.map.projection.srid
        };
        var layer = new ol.layer.Tile({
            visible: item.visible,
            gutter: 6,
            source: new ol.source.TileWMS(item.layer)
        });
        layer.set('legendURL', createLegendUrl(item.layer['url'], item.layer.content.seo_slug, config.map.projection.srid));
        return layer;
    };
    
    /**
     * Create GeoPackage layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Vector|Map.createLayerGeoPackage.layer}
     */
    var createLayerGeoPackage = function (item) {
        var style;
        var wkb, geometry, format = new ol.format.GeoJSON();
        var url = c.baseURL + '/storage/layer/' + item.layer.id + '/geopackage.json';
        var options = {
            dataProjection: 'EPSG:' + item.layer.projection_id,
            featureProjection: 'EPSG:' + config.map.projection.srid
        };
        
        // Add projection
        addLayerProjection(item.layer);

        // Load features
        function loadFeatures(extent, resolution, projection) {
            $http.get(url)
            .success(function (r) {
                if (r.type !== 'FeatureCollection') {
                    console & console.warn('Not supported GeoJSON type');
                }

                // If geometry is string assume WKB hexadecimal and convert to GeoJSON 
                // using nodes modules Buffer and wkx
                if (r.features.length && (typeof r.features[0].geometry === 'string')) {
                    angular.forEach(r.features, function (f, i) {
                        wkb = new Buffer(f.geometry, 'hex');
                        geometry = wkx.Geometry.parse(wkb);
                        geometry.hasZ = false;
                        geometry.hasM = false;
                        f.geometry = geometry.toGeoJSON();
                    });
                }

                // Read JSON
                layer.getSource().addFeatures(format.readFeatures(r, options));
            });
        }

        var layer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [],
                loader: function (extent, resolution, projection) {
                    loadFeatures(extent, resolution, projection);
                }
            }),
            style: function (feature, resolution) {
                style = createStyle(item.layer, feature, resolution);
                return [style];
            }
        });
        return layer;
    };
    
    /**
     * Create Postgis layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Vector|Map.createLayerPostgis.layer}
     */
    var createLayerPostgis = function (item) {
        var style, features = [];
        var format = new ol.format.GeoJSON();
        var url = c.baseURL + '/storage/layer/' + item.layer.id + '/postgis.json';
        
        // Add projection
        addLayerProjection(item.layer);

        function loadFeatures(extent, resolution, projection) {
            $http.get(url)
            .success(function (response) {
                features = format.readFeatures(response, {
                    dataProjection: 'EPSG:' + item.layer.projection.srid,
                    featureProjection: 'EPSG:' + config.map.projection.srid
                });
                angular.forEach(features, function (f, i) {
                    layer.getSource().addFeature(f);
                });
            });
        }

        var layer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [],
                loader: function (extent, resolution, projection) {
                    loadFeatures(extent, resolution, projection);
                }
            }),
            style: function (feature, resolution) {
                style = createStyle(item.layer, feature, resolution);
                return [style];
            }
        });
        return layer;
    };
    
    /**
     * Create GeoJSON layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Vector|Map.createLayerGeoJSON.layer}
     */
    var createLayerGeoJSON = function (item) {
        var style, features = [];
        var format = new ol.format.GeoJSON();
        var url = c.baseURL + '/storage/layer/' + item.layer.id + '/geojson.json';

        function loadFeatures(extent, resolution, projection) {
            $http.get(url)
            .success(function (response) {
                features = format.readFeatures(response, {
                    featureProjection: 'EPSG:' + config.map.projection.srid
                });
                angular.forEach(features, function (f, i) {
                    layer.getSource().addFeature(f);
                });
            });
        }

        var layer = new ol.layer.Vector({
            source: new ol.source.Vector({
                features: [],
                loader: function (extent, resolution, projection) {
                    loadFeatures(extent, resolution, projection);
                }
            }),
            style: function (feature, resolution) {
                style = createStyle(item.layer, feature, resolution);
                return [style];
            }
        });
        return layer;
    };
    
    /**
     * Create feature style
     * 
     * @param {Object} item
     * @param {ol.Feature} feature
     * @param {float} resolution
     * @returns {Map.ol.style.Style}
     */
    var createStyle = function (item, feature, resolution)
    {
        var image = {src: ''};

        // Get static style
        var style = new ol.style.Style({
            fill: new ol.style.Fill({ color: item.ol_style_static_fill_color }),
            stroke: new ol.style.Stroke({
                width: parseInt((item.ol_style_static_stroke_width === '' ? 2 : item.ol_style_static_stroke_width)),
                color: (item.ol_style_static_stroke_color === '' ? '#000000' : item.ol_style_static_stroke_color)
            }),
            image: new ol.style.Circle({
                radius: parseInt((item.ol_style_static_stroke_width === '' ? 2 : item.ol_style_static_stroke_width)),
                fill: new ol.style.Fill({
                  color: (item.ol_style_static_fill_color === '' ? '#000000' : item.ol_style_static_fill_color)
                })
            })
        });
        if (item.ol_style_static_icon) {
            image.src = item.ol_style_static_icon;
            image.src = c.baseURL + '/storage/layer/' + item.id + '/' + image.src;
        }

        // Get feature style
        if (item.ol_style_field_fill_color) {
            style = new ol.style.Style({
                fill: new ol.style.Fill({ color: feature.get(item.ol_style_field_fill_color) }),
                stroke: new ol.style.Stroke({
                    width: parseInt(feature.get(item.ol_style_field_stroke_width)),
                    color: feature.get(item.ol_style_field_stroke_color)
                }),
                image: new ol.style.Circle({
                    radius: parseInt(feature.get(item.ol_style_field_stroke_width)),
                    fill: new ol.style.Fill({
                        color: feature.get(item.ol_style_field_fill_color)
                    })
                })
            });
        }
        if (item.ol_style_field_icon) {
            image.src = feature.get(item.ol_style_field_icon);
            image.src = c.baseURL + '/storage/layer/' + item.id + '/icons/' + image.src;
        }
        if (image.src !== '') {
            style = new ol.style.Style({
                image: new ol.style.Icon(image)
            });
        }

        // TODO
        /*
        if (imagem && item.text) {
            style = new ol.style.Style({
                image: new ol.style.Icon(imagem),
                text: new ol.style.Text({
                    font: item.text.font,
                    textAlign: item.text.textAlign,
                    fill: new ol.style.Fill(item.text.fill),
                    stroke: new ol.style.Stroke(item.text.stroke),
                    textBaseline: item.text.textBaseline,
                    text: feature.get(item.text.text)
                })
            });
        }*/

        return style;
    };
    
    /**
     * Return map builder API
     * 
     * @type type
     */
    var service = {
        
        getConfig: function () {
            return config;
        },
        
        getMap: function () {
            return map;
        },
        
        ready: function (cb) {
            var wait = setInterval(function () {
                if (buildStatus === 1) {
                    clearInterval(wait);
                    cb();
                }
            }, 500);
        }
    };

    return service;
}]);
