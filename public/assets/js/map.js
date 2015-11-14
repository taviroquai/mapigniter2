
var Map = function ($, Mustache, ol, proj4)
{
    /**
     * Holds map configuration
     * 
     * @type Object
     */
    var config;
    
    /**
     * Holds OpenLayers map instance
     * 
     * @type Map.ol.Map|Map.ol.Map
     */
    var map;
    
    /**
     * Holds the map selector
     * 
     * @type String
     */
    var mapEl = '#map';
    
    /**
     * Holds layer switcher selector
     * 
     * @type String
     */
    var layerSwitcherEl = '#layerSwitcher';
    
    /**
     * Holds the base layer switcher selector
     * 
     * @type String
     */
    var baseLayerSwitcherEl = '#baseLayerSwitcher';
    
    /**
     * Holds base layer switcher group template
     * 
     * @type String
     */
    var baseLayerSwitcherGroupTmpl = $('#layer_switcher_basegroup_tpl').html();
    
    /**
     * Holds base layer switcher item template
     * @type String
     */
    var baseLayerSwitcherItemTmpl = $('#layer_switcher_baseitem_tpl').html();
    
    /**
     * Holds the layer switcher selector
     * 
     * @type String
     */
    var layerSwitcheEl = '#layerSwitcher';
    
    /**
     * Holds the layer switcher group template
     * 
     * @type String
     */
    var layerSwitcherGroupTmpl = $('#layer_switcher_group_tpl').html();
    
    /**
     * Hold the layer switcher item template
     * 
     * @type String
     */
    var layerSwitcherItemTmpl = $('#layer_switcher_item_tpl').html();
    
    /**
     * Holds the map navigation selector
     * 
     * @type String
     */
    var mapNavigationEl = '#mapNavigation';
    
    /**
     * Holds the search input selector
     * 
     * @type String
     */
    var searchInputEl = '[name="query"]';
    
    /**
     * Holds the search form selector
     * 
     * @type String
     */
    var searchInputFormEl = '#searchForm';
    
    /**
     * Holds the search results container selector
     * 
     * @type String
     */
    var featureSearchResultsEl = '#featureSearchResults';
    
    /**
     * Holds the search clear selector
     * 
     * @type String
     */
    var searchClearEl = '#featureSearchClear';
    
    /**
     * Holds the search results item template
     * 
     * @type String
     */
    var searchResultsItemTmpl = $('#search_result_item_tpl').html();
    
    /**
     * Loads map configuration from url
     * 
     * @param {string} url
     * @param {function} cb
     * @returns {Boolean}
     */
    var loadConfig = function (url, cb)
    {
        // Local variables
        var extent = [];
        
        // Validate url
        if (url && String(url).length === 0) {
            return false;
        }
        
        // Load configuration
        $.getJSON(url, function(c) {
            
            config = c;
    
            // Parse configuration items
            config.map.extent = config.map.projection.extent.split(' ');
            config.map.center = config.map.center.split(' ');
            config.map.center = [parseFloat(config.map.center[0]), parseFloat(config.map.center[1])];
            $.each(config.map.extent, function (i, item) {
                extent.push(parseFloat(item));
            });
            config.map.extent = extent;
            
            // Run callback
            if (typeof cb === 'function') {
                cb();
            }
        });
    };
    
    /**
     * Init map interface
     * 
     * @returns {Boolean}
     */
    var init = function ()
    {
        // Local variables
        var projection;
        
        if (config.map.projection.srid !== '3857' && config.map.projection.srid !== '4326' && config.map.projection.proj4_params !== '') {
            proj4.defs("EPSG:" + config.map.srid, config.map.projection.proj4_params);

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
                    zoom: 1
                })
            });

        } else {
            
            // Create regular OpenLayers map
            map = new ol.Map({
                target: 'map',
                layers: [],
                view: new ol.View({
                    center: ol.proj.transform([0, 0], 'EPSG:4326', 'EPSG:3857'),
                    zoom: 1
                })
            });
        }

        // Add layers
        addLayers();

        // Create layer switcher
        createLayerSwitcher();

        // Add click feature info
        createFeatureInfoEvent();

        // Add filter
        createFilter();

        // Create Navigation Events
        createNavigation();
        
        // Center map
        map.getView().setCenter(config.map.center);
        map.getView().setZoom(parseInt(config.map.zoom));
        
    };
    
    /**
     * Destroys current map
     * 
     * @returns {undefined}
     */
    var destroy = function () {
        $(layerSwitcherEl).empty();
        map.setTarget(null);
    };
    
    /**
     * Add layers to map from configuration
     * 
     * @returns {undefined}
     */
    var addLayers = function () {
        
        $.each(config.layers, function (i, item) {
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
            case "postgis":
                layer = createLayerPostgis(item);
                break;
            case "geojson":
                layer = createLayerGeoJSON(item);
                break;
            default:
                console ? console.log('Layer type not suported:', item.layer.type) : false;
            }
            if (layer) {
                layer.set('id', item.id);
                layer.set('title', item.layer.title);
                layer.set('group', item.group);
                layer.set('baselayer', item.baselayer);
                layer.set('content', item.layer.content);
                layer.set('template', item.layer.feature_info_template !== '' ? item.layer.feature_info_template : false);
                layer.set('search', item.layer.search ? item.layer.search.split(',') : false);
                map.addLayer(layer);
            }
        });
    };
    
    /**
     * Create layer switcher interface
     * 
     * @returns {undefined}
     */
    var createLayerSwitcher = function () {
        var group, group_id;

        function hideBaseLayers() {
            $(baseLayerSwitcherEl).find('option').each(function (i, item) {
                $(item).data('ol').setVisible(false);
            });
        }

        // Add to layer switcher
        map.getLayers().forEach(function (l) {
            group = l.get('group');
            l.set('group', 'Default');
            if (group) {
                l.set('group', group.content.title);
            }

            // Add base layers
            if (l.get('baselayer')) {
                if ($(baseLayerSwitcherEl).find('select').length === 0) {
                    $(baseLayerSwitcherEl).append(Mustache.render(baseLayerSwitcherGroupTmpl, l.getProperties()));
                    if (!$.inArray(l.get('group'), config.layerswitcher.closed)) {
                        $(baseLayerSwitcherEl).find('select').hide();
                    }
                }
                l.set('_visible', (l.getVisible() ? 'checked' : ''));
                $(baseLayerSwitcherEl).find('select').append(Mustache.render(baseLayerSwitcherItemTmpl, l.getProperties()));
                l.on('change:visible', function() {
                    if (l.getVisible()) {
                        $(baseLayerSwitcherEl).find('select').val(l.get('content').seo_slug);
                    }
                });
                $(baseLayerSwitcherEl).find('option[value="' + l.get('content').seo_slug + '"]').data('ol', l);
                $(baseLayerSwitcherEl).find('select').on('change', function (e) {
                    hideBaseLayers();
                    var value = $(this).val();
                    $(this).find('option').each(function (i, item) {
                        if (value === $(item).data('ol').get('content').seo_slug) {
                            $(item).data('ol').setVisible(true);
                        }
                    });
                });

            } else {

                // Add normal layers
                group_id = 'group_' + l.get('group').replace(/ /g, "");
                if ($(layerSwitcheEl + ' #' + group_id).length === 0) {
                    $(layerSwitcheEl).append(Mustache.render(layerSwitcherGroupTmpl, l.getProperties()));
                    $(layerSwitcheEl).append('<ul id="' + group_id + '" class="list-group" />');
                    if (!$.inArray(l.get('group'), config.layerswitcher.closed)) {
                        $(layerSwitcheEl + ' #' + group_id).hide();
                    }
                }
                l.set('_visible', (l.getVisible() ? 'checked' : ''));
                $(layerSwitcheEl + ' #' + group_id).append(Mustache.render(layerSwitcherItemTmpl, l.getProperties()));
                l.on('change:visible', function() {
                    $(layerSwitcheEl).find('input[data-layer="' + l.get('content').seo_slug + '"]').prop("checked", l.getVisible());
                });
                $(layerSwitcheEl).find('input[data-layer="' + l.get('content').seo_slug + '"]').data('ol', l);
                $(layerSwitcheEl).find('input[data-layer="' + l.get('content').seo_slug + '"]').on('click', function () {
                    $(this).data('ol').setVisible(!$(this).data('ol').getVisible());
                });
            }
        });

        // Turn on visible base layers
        hideBaseLayers();
        var selectedBaseLayer = $(baseLayerSwitcherEl).find('option[data-visible="checked"]').first();
        if (selectedBaseLayer.length && selectedBaseLayer.data('ol')) {
            selectedBaseLayer.data('ol').setVisible(true);
            $(baseLayerSwitcherEl).find('select').val(selectedBaseLayer.attr('value'));
        }

        // Activate group toggle event
        $(layerSwitcherEl).find('h4').on('click', function () {
            $(this).next('ul').toggle('slow');
        });
    };
    
    /**
     * Create navigation interface events
     * 
     * @returns {undefined}
     */
    var createNavigation = function ()
    {
        var history = [];
        var history_now = -1;
        var click = false;
        var wheel_delay = 350; // OpenLayers mouse wheel delay = 250

        // Add history
        $(mapNavigationEl).find('.action-nav-extent').on('click', function () {
            map.getView().setCenter(config.map.center);
            map.getView().setZoom(config.map.zoom);
        });

        // Zoom box interaction
        var zoomBox = new ol.interaction.DragZoom({
            condition: ol.events.condition.always,
            style: new ol.style.Style({
                stroke: new ol.style.Stroke({
                    color: [0,0,255,1]
                })
            })
        });
        map.removeInteraction(findInteraction(ol.interaction.DragZoom));
        map.addInteraction(zoomBox);
        zoomBox.on('boxend', function(e){
            $(mapNavigationEl).find('.action-nav-reset').click();
            zoomBox.setActive(false);
        });
        zoomBox.setActive(false);

        $(mapNavigationEl).find('.action-nav-reset').on('click', function () {
            findInteraction(ol.interaction.DragPan).setActive(true);
            zoomBox.setActive(false);
            $(mapEl).css('cursor', 'auto');
            $(mapNavigationEl).find('.action-nav-zoombox').removeClass('active');
        });
        $(mapNavigationEl).find('.action-nav-zoombox').on('click', function () {
            findInteraction(ol.interaction.DragPan).setActive(false);
            zoomBox.setActive(true);
            $(mapEl).css('cursor', 'crosshair');
            $(this).addClass('active');
        });
        $(mapNavigationEl).find('.action-nav-zoomout').on('click', function () {
            map.getView().setZoom(map.getView().getZoom()-1);
        });
        map.on('moveend', function (e) {

            // Do not save view history if previous/next was clicked
            if (click) return;
            history.push({center: map.getView().getCenter(), resolution: map.getView().getResolution()});
            history_now++;
        });
        $(mapNavigationEl).find('.action-nav-previous').on('click', function () {
            if (history_now > 0) {
                click = true;
                history_now--;
                map.getView().setCenter(history[history_now].center);
                map.getView().setResolution(history[history_now].resolution);
                setTimeout(function () {
                    click = false;
                }, wheel_delay);
            }
        });
        $(mapNavigationEl).find('.action-nav-next').on('click', function () {
            if (history_now + 1 < history.length) {
                click = true;
                history_now++;
                map.getView().setCenter(history[history_now].center);
                map.getView().setResolution(history[history_now].resolution);
                setTimeout(function () {
                    click = false;
                }, wheel_delay);
            }
        });

    };
    
    /**
     * Create search event
     * 
     * @returns {undefined}
     */
    var createFilter = function ()
    {
        var count, expr, tpl_data, cache = {};

        // Click result event
        $('body').on('click', featureSearchResultsEl + ' ul a', function () {
            var feature = cache[$(this).data('name')][parseInt($(this).data('index'))];
            if (feature.getGeometry() instanceof ol.geom.Point) {
                map.getView().setCenter(feature.getGeometry().getCoordinates());
                //map.getView().setZoom(8);
            } else {
                map.getView().fit(feature.getGeometry(), map.getSize());
            }
        });

        // Clear search
        $(searchClearEl).on('click', function () {
            $(searchInputEl).val('');
            $(searchInputFormEl).submit();
        });

        // Get search event
        $(searchInputFormEl).on('submit', function (e) {
            e.preventDefault();

            $(featureSearchResultsEl).find('ul').empty();
            $(featureSearchResultsEl).hide();
            cache = {};
            count = 0;
            expr = $(searchInputFormEl).find(searchInputEl).val()
                    .replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
            if (expr !== '') {
                $(featureSearchResultsEl).find('.no-results').hide();
                $(featureSearchResultsEl).show();
                map.getLayers().forEach(function(layer) {
                    if (layer instanceof ol.layer.Vector && layer.get('search')) {
                        cache[layer.get('content').seo_slug] = [];
                        $.each(layer.get('search'), function (i, attribute) {
                            var regex = new RegExp(expr, "i");
                            $.each(layer.getSource().getFeatures(), function(i, feature) {
                                if (feature.get(attribute) && regex.test(feature.get(attribute), 'i')) {
                                    // console.log(goog.getUid(feature).toString());
                                    tpl_data = {
                                        label: feature.get(attribute),
                                        layer: layer.get('content').title,
                                        name: layer.get('content').seo_slug,
                                        index: i
                                    };
                                    $(featureSearchResultsEl).find('ul').append(Mustache.render(searchResultsItemTmpl, tpl_data));
                                    cache[layer.get('content').seo_slug][i] = feature;
                                    count++;
                                }
                            });
                        });
                    }
                });
                if (count === 0) {
                    $(featureSearchResultsEl).find('.no-results').show();
                }
            }
        });
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
            source: new ol.source.TileWMS(config.layer)
        });
        return layer;
    };
    
    /**
     * Create WFS layer
     * 
     * @param {Object} item
     * @returns {Map.ol.layer.Vector}
     */
    var createLayerWFS = function (item) {
        
        var finalurl, features, style, format = new ol.format.WFS();
        
        function loadFeatures(url) {
            $.get(url, function (response) {
                features = format.readFeatures(response, {featureProjection: 'EPSG:' + config.map.projection.srid});
                $.each(features, function (i, item) {
                    source.addFeature(item);
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
            finalurl = config.layer.wfs_url
                + '&' + params.join('&') 
                + '&FILTER=' + encodeURIComponent('<Filter><PropertyIsLessThanOrEqualTo><PropertyName>' + config.zoom_attribute + '</PropertyName><Literal>' + map.getView().getZoom() + '</Literal></PropertyIsLessThanOrEqualTo></Filter>');
        } else {
            var source = new ol.source.Vector({
                strategy: ol.loadingstrategy.bbox,
                loader: function (extent, resolution, projection) {
                    finalurl = config.layer.wfs_url + "&" + params.join('&') + '&BBOX=' + extent.join(',') + ',EPSG:' + config.map.projection.srid;
                    loadFeatures(finalurl);
                }
            });
        }

        map.on('moveend', function () {
            if (typeof config.zoomAttribute !== 'undefined') {
                finalurl = config.url + '&FILTER=' + encodeURIComponent('<Filter><PropertyIsLessThanOrEqualTo><PropertyName>' + config.zoomAttribute + '</PropertyName><Literal>' + map.getView().getZoom() + '</Literal></PropertyIsLessThanOrEqualTo></Filter>');
                source.clear();
                loadFeatures(finalurl);
            }
        });

        var layer = new ol.layer.Vector({
            visible: config.visible,
            source: source,
            style: function (feature, resolution) {
                style = createStyle(config.layer, feature, resolution);
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
                url: APP_URL + '/storage/layer/' + item.layer.id + '/' + item.layer.gpx_filename,
                format: new ol.format.GPX()
            }),
            style: function (feature, resolution) {
                style = createStyle(config.layer, feature, resolution);
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
                url: APP_URL + '/storage/layer/' + item.layer.id + '/' + item.layer.kml_filename,
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
        
        config.layer['serverType'] = 'mapserver';
        config.layer['url'] = item.layer.shapefile_wmsurl;
        config.layer['params'] = {
            'LAYERS': item.layer.content.seo_slug,
            'TILED': false,
            'VERSION': '1.1.1',
            'SRS': 'EPSG:' + item.layer.projection_id,
            'CRS': 'EPSG:' + item.layer.projection_id
        };
        var layer = new ol.layer.Tile({
            visible: item.visible,
            gutter: 6,
            source: new ol.source.TileWMS(item.layer)
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
        var url = APP_URL + '/storage/layer/' + item.layer.id + '/postgis.json';

        function loadFeatures(extent, resolution, projection) {
            $.get(url, function (response) {
                features = format.readFeatures(response, {
                    featureProjection: 'EPSG:' + config.map.projection.srid
                });
                $.each(features, function (i, item) {
                    layer.getSource().addFeature(item);
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
                style = createStyle(config.layer, feature, resolution);
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
        var url = APP_URL + '/storage/layer/' + item.layer.id + '/geojson.json';

        function loadFeatures(extent, resolution, projection) {
            $.get(url, function (response) {
                features = format.readFeatures(response, {
                    featureProjection: 'EPSG:' + config.map.projection.srid
                });
                $.each(features, function (i, item) {
                    layer.getSource().addFeature(item);
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
     * Create feature info event handler
     * 
     * @returns {undefined}
     */
    var createFeatureInfoEvent = function () {
        
        map.on('singleclick', function (evt) {
            
            // Search features on mouse position
            var features = [], html;
            var pixel = map.getEventPixel(evt.originalEvent);
            map.forEachFeatureAtPixel(pixel, function (feature, layer) {
                feature.layer = layer;
                features.push(feature);
            });
            
            // Get feature attributes and show HTML
            $('#featureInfo').html();
            if (features.length > 0) {
                $('#content').collapse('show');
                if (features[0].layer.get('template')) {
                    html = Mustache.render(features[0].layer.get('template'), features[0].getProperties());
                    $('#featureInfo').html(html);
                }
            }
        });

        // Capture mouse move event and change pointer over feature
        map.on('pointermove', function (evt) {
            if (evt.dragging) {
                return;
            }
            // Get features on mouse position
            var pixel = map.getEventPixel(evt.originalEvent);
            var features = [];
            map.forEachFeatureAtPixel(pixel, function (feature, layer) {
                features.push(feature);
            });
            
            // Change pointer
            if (features.length > 0) {
                $(mapEl).css('cursor', 'pointer');
            } else {
                $(mapEl).css('cursor', '');
            }
        });
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
        var imagem = {src: ''}, self = this;

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
            imagem.src = item.ol_style_static_icon;
            imagem.src = APP_URL + '/storage/layer/' + item.id + '/' + imagem.src;
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
            imagem.src = feature.get(item.ol_style_field_icon);
            imagem.src = APP_URL + '/storage/layer/' + item.id + '/icons/' + imagem.src;
        }
        if (imagem.src !== '') {
            style = new ol.style.Style({
                image: new ol.style.Icon(imagem)
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
     * Find OpenLayers interaction
     * 
     * @param {String} classname
     * @returns {ol.Interaction|Boolean}
     */
    var findInteraction = function (classname) {
        
        var result = false;
        map.getInteractions().forEach(function(item) {
            if (item instanceof classname) {
                result = item;
            }
        });
        return result;
    };
    
    /**
     * Map application API
     * 
     * @param {String} url
     * @returns {Object}
     */
    return {
        
        init: function (url) {
            loadConfig(url, init);
        }
        
    };
};
