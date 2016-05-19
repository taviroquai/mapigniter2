
var ogc = function ($, url)
{
    /**
     * Private vars
     * @type String
     */
    var response = '', xmlDoc = null, capabilities = [];
    
    /**
     * Build GetCapabilities URL
     */
    var getCapabilitiesUrl = function (version)
    {
        var params = [];
        params.push('SERVICE=WMS');
        params.push('VERSION=' + version);
        params.push('REQUEST=GetCapabilities');
        return url + (url.indexOf('?') === -1 ? '?' : '') + params.join('&');
    };
    
    /**
     * Load XML document
     */
    var loadXMLDocument = function () {
        if (window.DOMParser) {
            xmlDoc = new DOMParser();
            xmlDoc.parseFromString(response, "text/xml");
        } else if (window.ActiveXObject) {
            xmlDoc = new ActiveXObject ("Microsoft.XMLDOM");
            xmlDoc.async = false;
            xmlDoc.loadXML(response);
        } else {
            return false;
        }
        return true;
    };
    
    /**
     * Get layer group SRS
     * 
     * @param {type} node
     * @returns {undefined}
     */
    var getLayerSRS = function (node)
    {
        var nodes, result = [];
        nodes = node.getElementsByTagName('SRS');
        for (var j = 0; j < nodes.length; j++) {
            if (result.indexOf(nodes[j].textContent) === -1) {
                result.push(nodes[j].textContent);
            }
        }
        nodes = node.getElementsByTagName('CRS');
        for (var j = 0; j < nodes.length; j++) {
            if (result.indexOf(nodes[j].textContent) === -1) {
                result.push(nodes[j].textContent);
            }
        }
        return result;
    };
    
    /**
     * Get layer SRS bounding box
     * 
     * @param {type} node
     * @param {type} srs
     * @returns {Array}
     */
    var getSRSBoundingBox = function (node, srs)
    {
        var nodes, result = [];
        nodes = node.getElementsByTagName('BoundingBox');
        for (var j = 0; j < nodes.length; j++) {
            if (nodes[j].getAttribute('SRS') === srs) {
                srs = nodes[j].getAttribute('SRS');
                result = [
                    parseFloat(nodes[j].getAttribute('minx')),
                    parseFloat(nodes[j].getAttribute('miny')),
                    parseFloat(nodes[j].getAttribute('maxx')),
                    parseFloat(nodes[j].getAttribute('maxx'))
                ];
            }
        }
        return result;
    };
    
    /**
     * Get layers
     */
    var getLayers = function (node)
    {
        var layer, i, j, result = [], nodes = node.getElementsByTagName('Layer');
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
            layer.title = nodes[i].getElementsByTagName('Title')[0].textContent;
            
            // Add abstract
            if (nodes[i].getElementsByTagName('Abstract').length) {
                layer.description = nodes[i].getElementsByTagName('Abstract')[0].textContent;
            }

            // Add Layer
            result.push(layer);
        }
        return result;
    };
    
    /**
     * Parse response
     * 
     * @param {type} response
     * @returns {undefined}
     */
    var parse = function () {

        var nodes, group, i, j;
        
        // Get Layers
        console.log(response);
        nodes = $(response).find('Capability > Layer');
        console.log(nodes);
        for (i = 0; i < nodes.length; i += 1) {
            
            // Create layer group
            group = {
                name: nodes[i].getElementsByTagName('Name')[0].textContent,
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
            capabilities.push(group);
        }
    };
    
    /**
     * Get capabilities
     * 
     * @param {type} url
     * @returns {undefined}
     */
    this.getCapabilities = function ()
    {
        $.ajax({
            type: "GET",
            url: getCapabilitiesUrl(url, '1.1.0'),
            dataType: "xml",
            success: function (xml) {
                response = xml;
                parse();
                console.log(capabilities);
            },
            error: function () {
                alert("The XML File could not be processed correctly.");
            }
        });
    };
    
    /**
     * Build layer's legend URL
     */
    this.getLayerLegendUrl = function (name, format, version, width, height)
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
     * Source Url
     */
    this.url = url;
    
};