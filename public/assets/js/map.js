
var Map = function ($, config)
{
    
    var self = this;
    
    self.config = config;
    
    self.config.map.extent = self.config.map.projection.extent.split(' ');
    self.config.map.center = self.config.map.center.split(' ');
    self.config.map.center = [parseFloat(self.config.map.center[0]), parseFloat(self.config.map.center[1])];
    var extent = [];
    $.each(self.config.map.extent, function (i, item) {
        extent.push(parseFloat(item));
    });
    self.config.map.extent = extent;
    
    if (self.config.map.projection.srid !== '3857' && self.config.map.projection.srid !== '4326' && self.config.map.projection.proj4_params !== '') {
        proj4.defs("EPSG:" + self.config.map.srid, self.config.map.projection.proj4_params);
        /*
        var projection = new ol.proj.Projection({
            code: 'EPSG:' + self.config.map.srid,
            extent: [-127101.82, -300782.39, 160592.41, 278542.12]
            //extent: self.config.map.extent
        });
        */
        var projection = new ol.proj.Projection({
            code: 'EPSG:' + self.config.map.projection.srid,
            units: 'm'
        });
        ol.proj.addProjection(projection);
        
        self.map = new ol.Map({
            target: 'map',
            layers: [],
            view: new ol.View({
                projection: projection,
                extent: self.config.map.extent,
                center: self.config.map.center,
                zoom: 1
            })
        });
        
    } else {
        self.map = new ol.Map({
            target: 'map',
            layers: [],
            view: new ol.View({
                center: ol.proj.transform([0, 0], 'EPSG:4326', 'EPSG:3857'),
                zoom: 1
            })
        });
    }
    
    // Add layers
    self.addLayers();
    
    // Center map
    self.map.getView().setCenter(self.config.map.center);
    self.map.getView().setZoom(parseInt(self.config.map.zoom));
    
    // Create layer switcher
    self.createLayerSwitcher();
    
    // Add click feature info
    self.createFeatureInfoEvent();
    
    // Add filter
    self.createFilter();
    
    // Create Navigation Events
    self.createNavigation();
    
};

// Destroi map
Map.prototype.destroy = function () {
    var self = this;
    $('#layerSwitcher').empty();
    self.map.setTarget(null);
};

// Adicionar camadas
Map.prototype.addLayers = function () {
    var self = this;
    $.each(self.config.layers, function (i, item) {
        var layer = false;
        switch (item.layer.type) {
        case "mapquest":
            layer = self.createLayerMapQuest(item);
            break;
        case "osm":
            layer = self.criarLayerOSM(item);
            break;
        case "opencyclemap":
            layer = self.createLayerOpenCycleMap(item);
            break;
        case "bing":
            layer = self.createLayerBing(item);
            break;
        case "wms":
            layer = self.createLayerWMS(item);
            break;
        case "wfs":
            layer = self.createLayerWFS(item);
            break;
        case "gpx":
            layer = self.createLayerGPX(item);
            break;
        case "kml":
            layer = self.createLayerKML(item);
            break;
        case "shapefile":
            layer = self.createLayerShapefile(item);
            break;
        case "postgis":
            layer = self.createLayerPostgis(item);
            break;
        case "geojson":
            layer = self.createLayerGeoJSON(item);
            break;
        default:
            console ? console.log('Layer type not suported:', item.layer.type) : false;
        }
        if (layer) {
            layer.set('id', item.id);
            layer.set('title', item.layer.title);
            layer.set('group', item.group);
            layer.set('content', item.layer.content);
            layer.set('template', item.layer.feature_info_template !== '' ? item.layer.feature_info_template : false);
            layer.set('search', item.layer.search ? item.layer.search.split(',') : false);
            self.map.addLayer(layer);
        }
    });
};

// Create layer switcher
Map.prototype.createLayerSwitcher = function () {
    var group, group_id, self = this;
    
    // Add to layer switcher
    self.map.getLayers().forEach(function (camada) {
        group = camada.get('group');
        camada.set('group', 'Default');
        if (group) {
            camada.set('group', group.content.title);
        }
        group_id = 'group_' + camada.get('group').replace(/ /g, "");
        if ($('#layerSwitcher #' + group_id).length === 0) {
            $('#layerSwitcher').append(Mustache.render($('#layer_switcher_group_tpl').html(), camada.getProperties()));
            $('#layerSwitcher').append('<ul id="' + group_id + '" class="list-group" />');
            if (!$.inArray(camada.get('group'), self.config.layerswitcher.closed)) {
                $('#layerSwitcher #' + group_id).hide();
            }
        }
        camada.set('_visible', (camada.getVisible() ? 'checked' : ''));
        $('#layerSwitcher #' + group_id).append(Mustache.render($('#layer_switcher_item_tpl').html(), camada.getProperties()));
        camada.on('change:visible', function() {
            $('#layerSwitcher input[data-layer="' + camada.get('content').seo_slug + '"]').prop("checked", camada.getVisible());
        });
        $('#layerSwitcher input[data-layer="' + camada.get('content').seo_slug + '"]').data('ol', camada);
        $('#layerSwitcher input[data-layer="' + camada.get('content').seo_slug + '"]').on('click', function () {
            $(this).data('ol').setVisible(!$(this).data('ol').getVisible());
        });
        
    });
    
    $('#layerSwitcher h4').on('click', function () {
        $(this).next('ul').toggle('slow');
    });
};

Map.prototype.createNavigation = function ()
{
    var self = this;
    var history = [];
    var history_now = -1;
    var click = false;
    var wheel_delay = 350; // OpenLayers mouse wheel delay = 250
    
    // Add history
    $('#mapNavigation .action-nav-extent').on('click', function () {
        self.map.getView().setCenter(self.config.map.center);
        self.map.getView().setZoom(self.config.map.zoom);
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
    self.map.removeInteraction(self.findInteraction(ol.interaction.DragZoom));
    self.map.addInteraction(zoomBox);
    zoomBox.on('boxend', function(e){
        $('#mapNavigation .action-nav-reset').click();
        zoomBox.setActive(false);
    });
    zoomBox.setActive(false);
    
    $('#mapNavigation .action-nav-reset').on('click', function () {
        self.findInteraction(ol.interaction.DragPan).setActive(true);
        zoomBox.setActive(false);
        $('#map').css('cursor', 'auto');
        $('#ribbon .action-nav-zoombox').removeClass('active');
    });
    $('#mapNavigation .action-nav-zoombox').on('click', function () {
        self.findInteraction(ol.interaction.DragPan).setActive(false);
        zoomBox.setActive(true);
        $('#map').css('cursor', 'crosshair');
        $(this).addClass('active');
    });
    $('#mapNavigation .action-nav-zoomout').on('click', function () {
        self.map.getView().setZoom(self.map.getView().getZoom()-1);
    });
    self.map.on('moveend', function (e) {
        
        // Do not save view history if previous/next was clicked
        if (click) return;
        history.push({center: self.map.getView().getCenter(), resolution: self.map.getView().getResolution()});
        history_now++;
    });
    $('#mapNavigation .action-nav-previous').on('click', function () {
        if (history_now > 0) {
            click = true;
            history_now--;
            self.map.getView().setCenter(history[history_now].center);
            self.map.getView().setResolution(history[history_now].resolution);
            setTimeout(function () {
                click = false;
            }, wheel_delay);
        }
    });
    $('#mapNavigation .action-nav-next').on('click', function () {
        if (history_now + 1 < history.length) {
            click = true;
            history_now++;
            self.map.getView().setCenter(history[history_now].center);
            self.map.getView().setResolution(history[history_now].resolution);
            setTimeout(function () {
                click = false;
            }, wheel_delay);
        }
    });
    
};


// Search vector features
Map.prototype.createFilter = function ()
{
    var self = this;
    var count, expr, tpl_data, cache = {};
    
    // Click result event
    $('body').on('click', '#featureSearchResults ul a', function () {
        var feature = cache[$(this).data('name')][parseInt($(this).data('index'))];
        if (feature.getGeometry() instanceof ol.geom.Point) {
            self.map.getView().setCenter(feature.getGeometry().getCoordinates());
            //self.map.getView().setZoom(8);
        } else {
            self.map.getView().fit(feature.getGeometry(), self.map.getSize());
        }
    });
    
    // Clear search
    $('#featureSearchClear').on('click', function () {
        $('#search').val('');
        $('#searchForm').submit();
    });
    
    // Get search event
    $('#searchForm').on('submit', function (e) {
        e.preventDefault();
        
        $('#featureSearchResults ul').empty();
        $('#featureSearchResults').hide();
        cache = {};
        count = 0;
        expr = $('#search').val().replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
        if (expr !== '') {
            $('#featureSearchResults .no-results').hide();
            $('#featureSearchResults').show();
            self.map.getLayers().forEach(function(layer) {
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
                                $('#featureSearchResults ul').append(Mustache.render($('#search_result_item_tpl').html(), tpl_data));
                                cache[layer.get('content').seo_slug][i] = feature;
                                count++;
                            }
                        });
                    });
                }
            });
            if (count === 0) {
                $('#featureSearchResults .no-results').show();
            }
        }
    });
};


// Camada MapQuest
Map.prototype.createLayerMapQuest = function (config) {
    
    var layer = new ol.layer.Tile({
        source: new ol.source.MapQuest({layer: 'sat'})
    });
    return layer;
};

// Camada Bing
Map.prototype.createLayerBing = function (config) {
    config.layer['key'] = config.layer.bing_key;
    config.layer['imagerySet'] = config.layer.bing_imageryset;
    var layer = new ol.layer.Tile({
        visible: config.visible,
        source: new ol.source.BingMaps(config.layer)
    });
    return layer;
};

Map.prototype.criarLayerOSM = function (config) {
    var layer = new ol.layer.Tile({
        source: new ol.source.OSM()
    });
    return layer;
};

Map.prototype.createLayerOpenCycleMap = function (config) {
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

// Camada raster WMS
Map.prototype.createLayerWMS = function (config) {
    var self = this;
    config.layer['serverType'] = config.layer.wms_servertype;
    config.layer['url'] = config.layer.wms_url;
    config.layer['params'] = {
        'LAYERS': config.layer.wms_layers,
        'TILED': config.layer.wms_tiled,
        'VERSION': config.layer.wms_version,
        'SRS': 'EPSG:' + self.config.map.projection.srid,
        'CRS': 'EPSG:' + self.config.map.projection.srid
    };
    var layer = new ol.layer.Tile({
        visible: config.visible,
        source: new ol.source.TileWMS(config.layer)
    });
    return layer;
};

// Camada vetorial apartir de dados do Mapserver (WFS)
Map.prototype.createLayerWFS = function (config) {
    var self = this;
    var finalurl, features, estilo, format = new ol.format.WFS();
    
    function carregarFeatures(url) {
        $.get(url, function (response) {
            features = format.readFeatures(response, {featureProjection: 'EPSG:' + self.config.map.projection.srid});
            $.each(features, function (i, item) {
                dados.addFeature(item);
            });
        });
    }
    
    // Carregador de dados
    var params = [
        'SERVICE=WFS',
        'VERSION=' + config.layer.wfs_version,
        'REQUEST=GetFeature',
        'typename=' + config.layer.wfs_typename,
        'srsname=EPSG:' + self.config.map.projection.srid
    ];
    if (typeof config.zoom_attribute !== 'undefined') {
        var dados = new ol.source.Vector({
            features: [] 
        });
        finalurl = config.layer.wfs_url
            + '&' + params.join('&') 
            + '&FILTER=' + encodeURIComponent('<Filter><PropertyIsLessThanOrEqualTo><PropertyName>' + config.zoom_attribute + '</PropertyName><Literal>' + self.map.getView().getZoom() + '</Literal></PropertyIsLessThanOrEqualTo></Filter>');
    } else {
        var dados = new ol.source.Vector({
            strategy: ol.loadingstrategy.bbox,
            loader: function (extent, resolution, projection) {
                finalurl = config.layer.wfs_url + "&" + params.join('&') + '&BBOX=' + extent.join(',') + ',EPSG:' + self.config.map.projection.srid;
                carregarFeatures(finalurl);
            }
        });
    }
    
    self.map.on('moveend', function () {
        if (typeof config.zoomAttribute !== 'undefined') {
            finalurl = config.url + '&FILTER=' + encodeURIComponent('<Filter><PropertyIsLessThanOrEqualTo><PropertyName>' + config.zoomAttribute + '</PropertyName><Literal>' + self.map.getView().getZoom() + '</Literal></PropertyIsLessThanOrEqualTo></Filter>');
            dados.clear();
            carregarFeatures(finalurl);
        }
    });
    
    var layer = new ol.layer.Vector({
        visible: config.visible,
        source: dados,
        style: function (feature, resolution) {
            estilo = self.createStyle(config.layer, feature, resolution);
            return [estilo];
        }
    });
    return layer;
};

// GPX layer
Map.prototype.createLayerGPX = function (config) {
    var estilo, self = this;
    var layer = new ol.layer.Vector({
        visible: config.visible,
        source: new ol.source.Vector({
            url: self.config.base_url + '/storage/layer/' + config.layer.id + '/' + config.layer.gpx_filename,
            format: new ol.format.GPX()
        }),
        style: function (feature, resolution) {
            estilo = self.createStyle(config.layer, feature, resolution);
            return [estilo];
        }
    });
    return layer;
};

// KML layer
Map.prototype.createLayerKML = function (config) {
    var self = this;
    var layer = new ol.layer.Vector({
        source: new ol.source.Vector({
            url: self.config.base_url + '/storage/layer/' + config.layer.id + '/' + config.layer.kml_filename,
            format: new ol.format.KML()
        })
    });
    return layer;
};

// Camada raster WMS
Map.prototype.createLayerShapefile = function (config) {
    var self = this;
    config.layer['serverType'] = 'mapserver';
    config.layer['url'] = config.layer.shapefile_wmsurl;
    config.layer['params'] = {
        'LAYERS': config.layer.content.seo_slug,
        'TILED': false,
        'VERSION': '1.1.1',
        'SRS': 'EPSG:' + config.layer.projection_id,
        'CRS': 'EPSG:' + config.layer.projection_id
    };
    var layer = new ol.layer.Tile({
        visible: config.visible,
        gutter: 6,
        source: new ol.source.TileWMS(config.layer)
    });
    return layer;
};

// Postgis layer
Map.prototype.createLayerPostgis = function (config) {
    var style, self = this, features = [];
    var format = new ol.format.GeoJSON();
    var url = self.config.base_url + '/storage/layer/' 
            + config.layer.id 
            + '/postgis.json';
    
    function carregarFeatures(extent, resolution, projection) {
        $.get(url, function (response) {
            features = format.readFeatures(response, {featureProjection: 'EPSG:' + self.config.map.projection.srid});
            $.each(features, function (i, item) {
                layer.getSource().addFeature(item);
            });
        });
    }
    
    var layer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [],
            loader: function (extent, resolution, projection) {
                carregarFeatures(extent, resolution, projection);
            }
        }),
        style: function (feature, resolution) {
            style = self.createStyle(config.layer, feature, resolution);
            return [style];
        }
    });
    return layer;
};

// GeoJSON layer
Map.prototype.createLayerGeoJSON = function (config) {
    var style, self = this, features = [];
    var format = new ol.format.GeoJSON();
    var url = self.config.base_url + '/storage/layer/' 
            + config.layer.id 
            + '/geojson.json';
    
    function carregarFeatures(extent, resolution, projection) {
        $.get(url, function (response) {
            features = format.readFeatures(response, {featureProjection: 'EPSG:' + self.config.map.projection.srid});
            $.each(features, function (i, item) {
                layer.getSource().addFeature(item);
            });
        });
    }
    
    var layer = new ol.layer.Vector({
        source: new ol.source.Vector({
            features: [],
            loader: function (extent, resolution, projection) {
                carregarFeatures(extent, resolution, projection);
            }
        }),
        style: function (feature, resolution) {
            style = self.createStyle(config.layer, feature, resolution);
            return [style];
        }
    });
    return layer;
};

// Capturar eventos do rato
Map.prototype.createFeatureInfoEvent = function () {
    var self = this;
    
    self.map.on('singleclick', function (evt) {
        // Procurar features na posição do clique
        var features = [];
        var pixel = self.map.getEventPixel(evt.originalEvent);
        self.map.forEachFeatureAtPixel(pixel, function (feature, layer) {
            feature.layer = layer;
            features.push(feature);
        });
        // Obter informacao e mostrar popup
        $('#featureInfo').html();
        if (features.length > 0) {
            $('#content').collapse('show');
            if (features[0].layer.get('template')) {
                $('#featureInfo').html(Mustache.render(features[0].layer.get('template'), features[0].getProperties()));
            }
        }
    });
    
    // Capturar evento mousemove e alterar ponteiro
    self.map.on('pointermove', function (evt) {
        if (evt.dragging) {
            return;
        }
        // Obter features na posição do ponteiro
        var pixel = self.map.getEventPixel(evt.originalEvent);
        var features = [];
        self.map.forEachFeatureAtPixel(pixel, function (feature, layer) {
            features.push(feature);
        });
        // Mudar ponteiro
        if (features.length > 0) {
            $('#' + self.map.getTarget()).css('cursor', 'pointer');
        } else {
            $('#' + self.map.getTarget()).css('cursor', '');
        }
    });
    
    // Obter extent ao fazer zoom e mostrar na consola
    self.map.on('moveend', function () {
        //console ? console.log(self.map.getView().calculateExtent(self.map.getSize()).join(',')) : false;
    });
};

Map.prototype.createStyle = function (config, feature, resolution)
{
    var imagem = {src: ''}, self = this;
    
    // Get static style
    var style = new ol.style.Style({
        fill: new ol.style.Fill({ color: config.ol_style_static_fill_color }),
        stroke: new ol.style.Stroke({
            width: parseInt((config.ol_style_static_stroke_width === '' ? 2 : config.ol_style_static_stroke_width)),
            color: (config.ol_style_static_stroke_color === '' ? '#000000' : config.ol_style_static_stroke_color)
        }),
        image: new ol.style.Circle({
            radius: parseInt((config.ol_style_static_stroke_width === '' ? 2 : config.ol_style_static_stroke_width)),
            fill: new ol.style.Fill({
              color: (config.ol_style_static_fill_color === '' ? '#000000' : config.ol_style_static_fill_color)
            })
        })
    });
    if (config.ol_style_static_icon) {
        imagem.src = config.ol_style_static_icon;
        imagem.src = self.config.base_url + '/storage/layer/' + config.id + '/' + imagem.src;
    }
    
    // Get feature style
    if (config.ol_style_field_fill_color) {
        style = new ol.style.Style({
            fill: new ol.style.Fill({ color: feature.get(config.ol_style_field_fill_color) }),
            stroke: new ol.style.Stroke({
                width: parseInt(feature.get(config.ol_style_field_stroke_width)),
                color: feature.get(config.ol_style_field_stroke_color)
            }),
            image: new ol.style.Circle({
                radius: parseInt(feature.get(config.ol_style_field_stroke_width)),
                fill: new ol.style.Fill({
                    color: feature.get(config.ol_style_field_fill_color)
                })
            })
        });
    }
    if (config.ol_style_field_icon) {
        imagem.src = feature.get(config.ol_style_field_icon);
        imagem.src = self.config.base_url + '/storage/layer/' + config.id + '/icons/' + imagem.src;
    }
    if (imagem.src !== '') {
        style = new ol.style.Style({
            image: new ol.style.Icon(imagem)
        });
    }
    
    // TODO
    /*
    if (imagem && config.text) {
        style = new ol.style.Style({
            image: new ol.style.Icon(imagem),
            text: new ol.style.Text({
                font: config.text.font,
                textAlign: config.text.textAlign,
                fill: new ol.style.Fill(config.text.fill),
                stroke: new ol.style.Stroke(config.text.stroke),
                textBaseline: config.text.textBaseline,
                text: feature.get(config.text.text)
            })
        });
    }*/
    
    return style;
};


Map.prototype.toggleInteraction = function (i) {
    var self = this;
    var j = 0;
    self.map.getInteractions().forEach(function(item) {
        item.setActive(i === j ? true : false);
        j++;
    });
};

Map.prototype.findInteraction = function (classname) {
    var self = this;
    var result = false;
    self.map.getInteractions().forEach(function(item) {
        if (item instanceof classname) {
            result = item;
        }
    });
    return result;
};
