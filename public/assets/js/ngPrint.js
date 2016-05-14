    
angular.module('ngMap')
.controller('ngPrint', ['$scope', '$window', 'ngMapBuilder',
function ($scope, $window, ngMapBuilder) {
    
    // Get map element
    var mapElement;
    
    // Layouts
    var layouts = {
        screen: {
            width: '100%',
            height: '100%'
        },
        a4v: {
            height: 842,
            width:  595
        }
    };
    $scope.selected = 'screen';
    
    // Responsive values
    $scope.extent = [];
    $scope.center = [];
    
    // Update map with selected layout
    $scope.updatePrintLayout = function () {
        var item = layouts[$scope.selected];
        angular.element(mapElement).css('width', item.width + 'px');
        angular.element(mapElement).css('height', item.height + 'px');
        ngMapBuilder.getMap().setSize([item.width, item.height]);
        $scope.extent = ngMapBuilder.getMap().getView().calculateExtent([item.width, item.height]);
        $scope.center = ngMapBuilder.getMap().getView().getCenter();
    };
    
    /**
     * Reset map size
     * 
     * @returns {undefined}
     */
    $scope.resetMapSize = function () {
        $scope.selected = 'screen';
        $scope.updatePrintLayout();
    };
    
    /**
     * Print map
     * @returns {undefined}
     */
    $scope.print = function () {
        
        // Add content to print-content
        angular.element('#print-version').empty();
        
        // Add layers content
        angular.element('#layerSwitcher li input[type="checkbox"]:checked').each(function (i, el) {
            var layer = angular.element(el).closest('li');
            angular.element('#print-version').append('<h4>' + angular.element(layer).find('label').text() + '</h4>');
            angular.element('#print-version').append(angular.element(layer).find('.layer-details').html());
        });
        
        // Add feature info content
        angular.element('#print-version').append(angular.element('#feature-info').html());
        
        // Call native print function
        $window.print();
    };
    
    /**
     * Init layer switcher
     * 
     * @returns {undefined}
     */
    var init = function () {
        
        // Setup defaults
        mapElement = ngMapBuilder.getMap().getTargetElement();
        layouts.screen.width = angular.element(mapElement).css('width').replace('px', '');
        layouts.screen.height = angular.element(mapElement).css('height').replace('px', '');
        
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

