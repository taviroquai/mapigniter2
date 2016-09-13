
;(function ( $ ) {
    
    
    /**
     * Returns a new - OGCService instance
     * @param {jQuery} el - The jQuery selected elements
     * @param {String} - The URL param
     * @returns {Progress}
     */
    var OGCService = function (el, url)
    {
        /**
         * Private vars
         * @type String
         */
        var wmsCapabilities = [],
            wfsCapabilities = [],
            wfsFeatures = [];

        /**
         * Parse layer SRS/CRS
         * 
         * @param {type} node
         * @returns {undefined}
         */
        var getLayerSRS = function (node)
        {
            var nodes, result = [];
            nodes = $(node).find('SRS');
            for (var j = 0; j < nodes.length; j++) {
                if (result.indexOf($(nodes[j]).text()) === -1) {
                    result.push($(nodes[j]).text());
                }
            }
            nodes = $(node).find('CRS');
            for (var j = 0; j < nodes.length; j++) {
                if (result.indexOf($(nodes[j]).text()) === -1) {
                    result.push($(nodes[j]).text());
                }
            }
            return result;
        };

        /**
         * Parse layer SRS bounding box
         * 
         * @param {type} node
         * @param {type} srs
         * @returns {Array}
         */
        var getSRSBoundingBox = function (node, srs)
        {
            var nodes, result = [];
            nodes = $(node).find('BoundingBox');
            for (var j = 0; j < nodes.length; j++) {
                if ($(nodes[j]).attr('SRS') === srs) {
                    srs = $(nodes[j]).attr('SRS');
                    result = [
                        parseFloat($(nodes[j]).attr('minx')),
                        parseFloat($(nodes[j]).attr('miny')),
                        parseFloat($(nodes[j]).attr('maxx')),
                        parseFloat($(nodes[j]).attr('maxx'))
                    ];
                }
            }
            return result;
        };

        /**
         * Parse layers
         * 
         * @param {object} node
         * @returns {Array}
         */
        var getLayers = function (node)
        {
            var layer, i, j, result = [], nodes = $(node).find('Layer');
            for (i = 0; i < nodes.length; i += 1) {

                // Find layer SRS or CRS
                layer = { title: '', description: '', srs: [] };

                // Get SRS and Bounding Boxes
                var srsList = getLayerSRS(nodes[i]);
                for (j = 0; j < srsList.length; j += 1) {
                    layer.srs.push({
                        name: srsList[j],
                        bbox: getSRSBoundingBox(nodes[i], srsList[j])
                    });
                }

                // Set layer title
                layer.title = $(nodes[i]).find('Title').first().text();
                
                // Set layer name
                if ($(nodes[i]).find('Name').length) {
                    layer.name = $(nodes[i]).find('Name').first().text();
                }

                // Set layer abstract
                if ($(nodes[i]).find('Abstract').length) {
                    layer.description = $(nodes[i]).find('Abstract').first().text();
                }

                // Add Layer
                result.push(layer);
            }
            return result;
        };

        /**
         * Parse WMS
         * 
         * @param {Object} response
         * @returns {undefined}
         */
        this.parseWMSCapabilities = function (response, version) {
            
            var nodes, group, i, j;

            // Get Layers
            nodes = $(response).find('Capability > Layer');
            
            for (i = 0; i < nodes.length; i += 1) {

                // Create layer group
                group = {
                    name: $(nodes[i]).find('Name').first().text(),
                    srs: [],
                    layers: []
                };

                // Get SRS and Bounding Box
                var srsList = getLayerSRS(nodes[i]);
                for (j = 0; j < srsList.length; j += 1) {
                    group.srs.push({
                        name: srsList[j],
                        bbox: getSRSBoundingBox(nodes[i], srsList[j])
                    });
                }

                // Get layers
                group.layers = getLayers(nodes[i]);

                // Add layer group
                wmsCapabilities[version] = wmsCapabilities[version] || [];
                wmsCapabilities[version].push(group);
            }
            return wmsCapabilities[version];
        };
        
        /**
         * Parse WFS
         * 
         * @param {Object} response
         * @returns {undefined}
         */
        this.parseWFSCapabilities = function (response, version) {
            
            var nodes, featureType, i, j;

            // Get Layers
            nodes = $(response).find('FeatureTypeList > FeatureType');
            for (i = 0; i < nodes.length; i += 1) {

                // Create layer group
                featureType = {
                    name: $(nodes[i]).find('Name').text(),
                    srs: []
                };

                // Get SRS and Bounding Box
                var srsList = getLayerSRS(nodes[i]);
                for (j = 0; j < srsList.length; j += 1) {
                    featureType.srs.push({
                        name: srsList[j],
                        bbox: []
                    });
                }
                
                // Set FeatureType title
                if ($(nodes[i]).find('Title').length) {
                    featureType.title = $(nodes[i]).find('Title').first().text();
                }

                // Add layer group
                wfsCapabilities[version] = wfsCapabilities[version] || [];
                wfsCapabilities[version].push(featureType);
            }
            return wfsCapabilities[version];
        };
        
        /**
         * TODO: boundedBy node
         * 
         * @param {type} node
         * @returns {unresolved}
         */
        var parseWFSBoundedBy = function (node)
        {
            var t = $(node).text().trim().split(' ');
            for(var i = 0; i < t.length; i++) {
                t[i] = t[i].split(',');
            }
            return t;
        };
        
        /**
         * TODO: Parse geometry node
         * 
         * @param {type} node
         * @returns {unresolved}
         */
        var parseWFSGeometry = function (node)
        {
            var t = $(node).text().trim().split(' ');
            for(var i = 0; i < t.length; i++) {
                t[i] = t[i].split(',');
            }
            return t;
        };
        
        /**
         * Parse WFS Features
         * 
         * @param {Object} response
         * @returns {undefined}
         */
        this.parseWFSGetFeature = function (response, typename) {
            
            var namespace, firstitem, feature, name;
            wfsFeatures[typename] = [];
            
            // Get namespace from feature member
            if ($(response).find('gml\\:featureMember').length) {
                
                // Use WFS 1.0.0
                firstitem = $(response).find('gml\\:featureMember').first().children().first()[0];
                namespace = firstitem.localName.indexOf(':') ?
                    firstitem.localName.substring(0, firstitem.localName.indexOf(':'))
                    : '';
            } else {
                
                // Use WFS 1.1.0
                firstitem = $(response).find('gml\\:featureMembers').first().children().first()[0];
                namespace = firstitem.localName.indexOf(':') ?
                    firstitem.localName.substring(0, firstitem.localName.indexOf(':'))
                    : '';
            }

            // Get features
            feature = {};

            // Get attributes
            $(firstitem).children().each(function(j, attribute) {
                name = attribute.localName.replace(namespace + ':', '');
                feature[name] = $.isNumeric($(attribute).text()) ? 
                    parseInt($(attribute).text()) : $(attribute).text();
            });

            // Add feature
            wfsFeatures[typename].push(feature);
            return wfsFeatures[typename];
        };
        
        /**
         * Run ajax request for XML
         * 
         * @param {String} url
         * @returns {jqXHR}
         */
        var requestXML = function (url)
        {
            return $.ajax({
                type: "GET",
                url: url,
                dataType: "xml",
                error: function () {
                    alert("The XML File could not be processed correctly.");
                }
            });
        };
        
        /**
         * Build GetCapabilities URL
         * @param {String} version
         * @returns {OGCService_L2.OGCService.url|String}
         */
        this.getCapabilitiesUrl = function (service, version)
        {
            var params = [];
            params.push('SERVICE=' + service);
            params.push('VERSION=' + version);
            params.push('REQUEST=GetCapabilities');
            return url + (url.indexOf('?') === -1 ? '?' : '') + params.join('&');
        };
        
        /**
         * Get WMS Capabilities
         * 
         * @param {String} url
         * @returns {undefined}
         */
        this.getCapabilities = function (service, version, cb)
        {
            // Request and parse capabilities
            var self = this;
            $.when(requestXML(getCapabilitiesUrl(service, version))).then(function (r) {
                
                // Call parse service
                switch(service) {
                    case 'WMS':
                        parseWMSCapabilities(r, version);
                        (typeof cb === 'function') ? cb.call(self, wmsCapabilities[version]) : null;
                        break;
                    case 'WFS':
                        parseWFSCapabilities(r, version);
                        (typeof cb === 'function') ? cb.call(self, wfsCapabilities[version]) : null;
                        break;
                    default: alert('Invalid OGC service name.');
                }
            });
        };

        /**
         * Build layer legend URL
         * 
         * @param {String} name
         * @param {String} format
         * @param {String} version
         * @param {Integer} width
         * @param {Integer} height
         * @returns {OGCService_L2.OGCService.url|String}
         */
        this.buildLayerLegendUrl = function (name, format, version, width, height)
        {
            var params = [];
            params.push('SERVICE=WMS');
            params.push('VERSION=' + version);
            params.push('REQUEST=GetLegendGraphic');
            params.push('FORMAT=' + format);
            params.push('WIDTH=' + width);
            params.push('HEIGHT=' + height);
            params.push('LAYER=' + name);
            return url + (url.indexOf('?') === -1 ? '?' : '&') + params.join('&');
        };
        
        /**
         * Build WFS get feature with filter
         * 
         * @param {String} version
         * @param {String} typename
         * @param {Object} filter
         * @returns {OGCService_L2.OGCService.url|String}
         */
        this.buildWFSGetFeatureUrl = function (version, typename, filter)
        {
            var params = [];
            params.push('SERVICE=WFS');
            params.push('VERSION=' + version);
            params.push('REQUEST=GetFeature');
            params.push('typename=' + typename);
            for(var a in filter) {
                params.push('a=' + filter[a]);
            }
            return url + (url.indexOf('?') === -1 ? '?' : '&') + params.join('&');
        };
        
        /**
         * Get feature attributes
         * 
         * @param {String} version
         * @param {Sting} typename
         * @param {Object} filter
         * @param {function} cb
         * @returns {undefined}
         */
        this.getWFSTypenameAttributes = function (version, typename, filter, cb)
        {
            // Request and parse capabilities
            var self = this;
            $.when(requestXML(self.buildWFSGetFeatureUrl(version, typename, filter))).then(function (r) {
                
                // Call parse service
                parseWFSGetFeature(r, typename);
                (typeof cb === 'function') ? cb.call(self, wfsFeatures[typename]) : null;
            });
        };

        /**
         * The service URL
         */
        this.url = url;

    };
    
    /**
     * Adds OGCSrvice to jQuery plugins
     * @param {Object} options - The plugin options
     * @returns {Progress}
     */
    $.fn.OGCService = function(url) {
        var p = new OGCService(this, url);
        return p;
    };

}( jQuery ));