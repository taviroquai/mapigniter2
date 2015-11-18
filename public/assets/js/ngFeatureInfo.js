    
angular.module('ngMap')
.controller('ngFeatureInfo', ['Mustache', '$scope', 'ngMapBuilder', 
function (Mustache, $scope, ngMapBuilder) {
    
    /**
     * Scope models
     */
    $scope.info = '';
    
    /**
     * Init seach results
     * 
     * @returns {undefined}
     */
    var init = function () {
        
        ngMapBuilder.getMap().on('singleclick', function (evt) {
            
            // Search features on mouse position
            var features = [];
            var pixel = ngMapBuilder.getMap().getEventPixel(evt.originalEvent);
            ngMapBuilder.getMap().forEachFeatureAtPixel(pixel, function (feature, layer) {
                feature.layer = layer;
                features.push(feature);
            });
            
            // Get feature attributes and show HTML
            $scope.info = '';
            if (features.length > 0) {
                if (features[0].layer.get('template')) {
                    $scope.info = Mustache.render(features[0].layer.get('template'), features[0].getProperties());
                }
            }
            
        });

        // Capture mouse move event and change pointer over feature
        ngMapBuilder.getMap().on('pointermove', function (evt) {
            if (evt.dragging) {
                return;
            }
            // Get features on mouse position
            var pixel = ngMapBuilder.getMap().getEventPixel(evt.originalEvent);
            var features = [];
            ngMapBuilder.getMap().forEachFeatureAtPixel(pixel, function (feature, layer) {
                features.push(feature);
            });
            
            // Change pointer
            if (features.length > 0) {
                angular.element(ngMapBuilder.getMap().getTargetElement()).css('cursor', 'pointer');
            } else {
                angular.element(ngMapBuilder.getMap().getTargetElement()).css('cursor', '');
            }
            
        });
        
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

