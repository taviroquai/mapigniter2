    
angular.module('ngMap')
.controller('ngSearchResults', ['$scope', 'ngMapBuilder',
function ($scope, ngMapBuilder) {
    
    /**
     * Scope models
     */
    $scope.results = [];
    $scope.query = '';
    $scope.hasResults = false;
    
    /**
     * Search features
     * TODO: needs more work
     * 
     * @returns {undefined}
     */
    var search = function () {
        
        var result;
        ngMapBuilder.getMap().getLayers().forEach(function(layer) {
            if (layer instanceof ol.layer.Vector && layer.get('search')) {
                $.each(layer.get('search'), function (i, attribute) {
                    var regex = new RegExp($scope.query, "i");
                    $.each(layer.getSource().getFeatures(), function(i, feature) {
                        if (feature.get(attribute) && regex.test(feature.get(attribute), 'i')) {
                            result = {
                                label: feature.get(attribute),
                                layer: layer.get('content').title,
                                name: layer.get('content').seo_slug,
                                index: i,
                                feature: feature
                            };
                            $scope.results.push(result);
                        }
                    });
                });
            }
        });
        
        // Set has results
        $scope.hasResults = true;
    };
    
    /**
     * Locate item position
     * 
     * @param {type} item
     * @returns {undefined}
     */
    $scope.locateItem = function (item) {
        if (item.feature.getGeometry() instanceof ol.geom.Point) {
            ngMapBuilder.getMap().getView().setCenter(item.feature.getGeometry().getCoordinates());
            //ngMapBuilder.getMap().getView().setZoom(8);
        } else {
            ngMapBuilder.getMap().getView().fit(item.feature.getGeometry(), ngMapBuilder.getMap().getSize());
        }
    }; 
    
    /**
     * Init seach results
     * 
     * @returns {undefined}
     */
    var init = function () {
        
        // Clear search results
        $scope.clearResults = function () {
            $scope.results = [];
            $scope.hasResults = false;
        };
        
        // Get search event
        $scope.doSearch = function () {
            $scope.clearResults();
            $scope.query = $scope.query.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
            if ($scope.query !== '') {
                search();
            }
        };
        
        // Apply
       $scope.$apply();
        
    };
    
    /**
     * Load map and build search ui
     */
    ngMapBuilder.ready(function () {
        init();
    });
    
}]);

