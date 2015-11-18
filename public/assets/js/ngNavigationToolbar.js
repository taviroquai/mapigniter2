    
angular.module('ngMap')
.controller('ngNavigationToolbar', ['$scope', 'ngMapBuilder', 'ol',
function ($scope, ngMapBuilder, ol) {
    
    /**
     * Scope models
     */
    $scope.zoomBoxEnable = false;
    
    var history = [];
    var history_now = -1;
    var click = false;
    var wheel_delay = 350; // OpenLayers mouse wheel delay = 250
    
    /**
     * Find OpenLayers interaction
     * 
     * @param {String} classname
     * @returns {ol.Interaction|Boolean}
     */
    var findInteraction = function (classname) {
        
        var result = false;
        ngMapBuilder.getMap().getInteractions().forEach(function(item) {
            if (item instanceof classname) {
                result = item;
            }
        });
        return result;
    };

    /**
     * Reset original view
     * 
     * @returns {undefined}
     */
    $scope.fullView = function () {
        ngMapBuilder.getMap().getView().setCenter(ngMapBuilder.getConfig().map.center);
        ngMapBuilder.getMap().getView().setZoom(ngMapBuilder.getConfig().map.zoom);
    };

    /**
     * Create zoom box interaction
     * 
     * @type ol.interaction.DragZoom
     */
    var zoomBox = new ol.interaction.DragZoom({
        condition: ol.events.condition.always,
        style: new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: [0,0,255,1]
            })
        })
    });
    
    /**
     * Reset navigation
     * 
     * @returns {undefined}
     */
    $scope.reset = function () {
        findInteraction(ol.interaction.DragPan).setActive(true);
        $scope.zoomBoxEnable = false;
        zoomBox.setActive($scope.zoomBoxEnable);
    };
    
    /**
     * Enable zoom box
     * 
     * @returns {undefined}
     */
    $scope.zoomBox = function () {
        findInteraction(ol.interaction.DragPan).setActive(false);
        $scope.zoomBoxEnable = true;
        zoomBox.setActive($scope.zoomBoxEnable);
        angular.element(ngMapBuilder.getMap().getTargetElement()).css('cursor', 'crosshair');
    };
    
    /**
     * Zoom out
     * 
     * @returns {undefined}
     */
    $scope.zoomOut = function () {
        ngMapBuilder.getMap().getView().setZoom(ngMapBuilder.getMap().getView().getZoom()-1);
    };
    
    /**
     * Go to previous view
     * 
     * @returns {undefined}
     */
    $scope.previousView = function () {
        if (history_now > 0) {
            click = true;
            history_now--;
            ngMapBuilder.getMap().getView().setCenter(history[history_now].center);
            ngMapBuilder.getMap().getView().setResolution(history[history_now].resolution);
            setTimeout(function () {
                click = false;
            }, wheel_delay);
        }
    };
    
    /**
     * Go to next view
     * 
     * @returns {undefined}
     */
    $scope.nextView = function () {
        if (history_now + 1 < history.length) {
            click = true;
            history_now++;
            ngMapBuilder.getMap().getView().setCenter(history[history_now].center);
            ngMapBuilder.getMap().getView().setResolution(history[history_now].resolution);
            setTimeout(function () {
                click = false;
            }, wheel_delay);
        }
    };
    
    /**
     * Init seach results
     * 
     * @returns {undefined}
     */
    var init = function () {
        
        /**
         * Init ol interactions
         */
        ngMapBuilder.getMap().removeInteraction(findInteraction(ol.interaction.DragZoom));
        ngMapBuilder.getMap().addInteraction(zoomBox);
        zoomBox.on('boxend', function(e){
            $scope.reset();
            $scope.zoomBoxEnable = false;
            zoomBox.setActive($scope.zoomBoxEnable);
            angular.element(ngMapBuilder.getMap().getTargetElement()).css('cursor', 'default');
        });
        zoomBox.setActive($scope.zoomBoxEnable);
        
        /**
        * Add view change to history
        */
       ngMapBuilder.getMap().on('moveend', function (e) {

           // Do not save view history if previous/next was clicked
           if (click) return;
           history.push({
               center: ngMapBuilder.getMap().getView().getCenter(),
               resolution: ngMapBuilder.getMap().getView().getResolution()
           });
           history_now++;
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

