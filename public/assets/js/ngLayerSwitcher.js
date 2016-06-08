    
angular.module('ngMap')
.controller('ngLayerSwitcher', ['$scope', 'ngMapBuilder',
function ($scope, ngMapBuilder) {
    
    /**
     * Scope models
     */
    $scope.baseLayers = [];
    $scope.groupLayers = [];
    $scope.layers = [];
    $scope.baseLayer = null;
    
    /**
     * On select base layer
     * 
     * @returns {undefined}
     */
    $scope.selectedBaseLayer = function () {
        toggleBaseLayer($scope.baseLayer);
    };
    
    /**
     * On click layer visibility
     * 
     * @returns {undefined}
     */
    $scope.toggleLayer = function (l) {
        l.ol.setVisible(l.visible);
    };
    
    /**
     * Toggle group visibility
     * 
     * @param {type} g
     * @returns {undefined}
     */
    $scope.toggleGroup = function(g) {
        g.visible = !g.visible;
    };
    
    /**
     * Zoom to layer extent
     * 
     * @param {type} l
     * @returns {undefined}
     */
    $scope.zoomLayer = function (l) {
        ngMapBuilder.getMap().getView().fit(
            l.ol.getSource().getExtent(),
            ngMapBuilder.getMap().getSize()
        );
    };
    
    /**
     * Toggle base layer
     * 
     * @param {type} layer
     * @returns {undefined}
     */
    var toggleBaseLayer = function (layer) {
        angular.forEach($scope.baseLayers, function (item) {
            if (item === layer) {
                item.ol.setVisible(true);
            } else {
                item.ol.setVisible(false);
            }
        });
    };
    
    /**
     * Initiate base layer
     * 
     * @returns {undefined}
     */
    var initBaseLayer = function () {
        angular.forEach($scope.baseLayers, function (item) {
            if ($scope.baseLayer === null && item.visible) {
                $scope.baseLayer = item;
                toggleBaseLayer($scope.baseLayer);
            }
        });
    };
    
    /**
     * Find base layer group
     * 
     * @param {type} id
     * @returns {item|Boolean}
     */
    var findOrNewGroup = function (l) {
        var group = false;
        angular.forEach($scope.groupLayers, function (item) {
            if (item.id === l.get('content').seo_slug) {
                group = item;
            }
        });
        if (!group) {
            group = {
                id: l.get('content').seo_slug,
                title: l.get('content').title,
                visible: l.getVisible(),
                layers: []
            };
            $scope.groupLayers.push(group);
        }
        return group;
    };
    
    /**
     * Add layer to group
     * 
     * @param {type} layer
     * @param {type} group
     * @returns {undefined}
     */
    var addLayer = function(layer, group) {
        var item = layer.getProperties();
        item['ol'] = layer;
        group.push(item);
    }
    
    /**
     * Init layer switcher
     * 
     * @returns {undefined}
     */
    var init = function () {
        
        var group;
        
        ngMapBuilder.getMap().getLayers().forEach(function (l) {
            
            if (l.get('baselayer')) {
                addLayer(l, $scope.baseLayers);
            } else {
                if (l instanceof ol.layer.Group) {
                    group = findOrNewGroup(l);
                    l.getLayers().forEach(function (item) {
                        addLayer(item, group.layers);
                    });
                } else {
                    addLayer(l, $scope.layers);
                }
            }
        });
        
        // Init default base layer
        initBaseLayer();
        
        // Apply
       $scope.$apply();
        
    };
    
    /**
     * Load map and build layer switcher
     */
    ngMapBuilder.ready(function () {
        init();
    });
    
}]);

