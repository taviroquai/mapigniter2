    
angular.module('ngMap')
.controller('ngIdiom', ['$scope', 'config', '$window',
function ($scope, config, $window) {
    
    /**
     * Scope models
     */
    $scope.selected = config.idiomId;
    $scope.idioms = config.idioms;
    
    /**
     * Change page URL with idiom
     * 
     * @returns {undefined}
     */
    $scope.changeIdiom = function () {
        $window.location.href = config.baseURL + '/idiom/' + $scope.selected;
    };
    
}]);

