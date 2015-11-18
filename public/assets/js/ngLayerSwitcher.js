    
angular.module('ngMap')
.controller('ngLayerSwitcher', ['$scope', 'ngMapBuilder',
function ($scope, ngMapBuilder) {
    
    /**
     * Scope models
     */
    $scope.baseLayers = [];
    $scope.groupLayers = [];
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
    var getGroup = function (id) {
        var result = false;
        angular.forEach($scope.groupLayers, function (item) {
            if (item.id === id) {
                result = item;
            }
        });
        return result;
    };
    
    /**
     * Init layer switcher
     * 
     * @returns {undefined}
     */
    var init = function () {
        
        var group, layer;
        
        ngMapBuilder.getMap().getLayers().forEach(function (l) {
            
            layer = l.getProperties();
            layer['ol'] = l;
            
            if (l.get('baselayer')) {
                
                $scope.baseLayers.push(layer);
                
            } else {
                
                group = getGroup(l.get('group') ? l.get('group').content.seo_slug : 'default');
                if (!group) {
                    if (l.get('group')) {
                        group = {
                            id: l.get('group').content.seo_slug,
                            title: l.get('group').content.title,
                            layers: []
                        };
                    } else {
                        group = {
                            id: 'default',
                            title: 'Default',
                            layers: []
                        };
                    }
                    $scope.groupLayers.push(group);
                }
                
                group.layers.push(layer);
                
            }
        });
        
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

