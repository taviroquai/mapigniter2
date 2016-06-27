    
angular.module('ngMap')
.controller('ngContent', ['$scope', '$http',
function ($scope, $http) {
    
    /**
     * Vars
     */
    $scope.url = $scope.url || null;
    $scope.title = $scope.title || null;
    $scope.target = $scope.target || null;
    
    /**
     * Open content in modal
     * 
     * @param {String} content
     * @returns {undefined}
     */
    var openModal = function (content) {
        var modal = angular.element($scope.target);
        modal.find('.modal-title').text($scope.title);
        modal.find('.modal-body').html(content);
        modal.modal('show');
    };
    
    /**
     * Change page URL with idiom
     * 
     * @returns {undefined}
     */
    $scope.show = function () {
        $http.get($scope.url)
        .success(openModal);
    };
    
}]);

