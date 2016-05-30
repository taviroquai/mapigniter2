    
angular.module('ngMap')
.controller('ngSearchResults', ['$scope', 'ngMapBuilder', 'lunr', 
function ($scope, ngMapBuilder, lunr) {
    
    /**
     * Scope models
     */
    $scope.results = [];
    $scope.query = '';
    $scope.hasResults = false;
    
    /**
     * Items store
     * 
     * @type {Array}
     */
    var store = [];
    
    /**
     * Instantiate lunr text search
     * 
     * @type @call;lunr
     */
    var index = lunr(function () {
        this.field('label', {boost: 10});
        this.ref('id');
    });
    
    /**
     * Build text search index
     */
    function buildSearchIndex()
    {
        var item;
        
        ngMapBuilder.getMap().getLayers().forEach(function(layer) {
            if (layer instanceof ol.layer.Vector && layer.get('search')) {
                $.each(layer.get('search'), function (i, attribute) {
                    $.each(layer.getSource().getFeatures(), function(i, feature) {
                        item = {
                            id: layer.get('content').seo_slug + '.' + i,
                            label: feature.get(attribute),
                            layer: layer,
                            name: layer.get('content').title,
                            attribute: attribute,
                            index: i,
                            feature: feature
                        };
                        store[item.id] = item;
                        index.add({id: item.id, label: item.label});
                    });
                });
            }
        });
    }
    
    /**
     * Search features
     * 
     * @returns {undefined}
     */
    var search = function () {
        
        // Lazy loading to build search index
        if (store.length === 0) {
            buildSearchIndex();
        }
        
        // Search index
        var results = index.search($scope.query);
        $.each(results, function (i, item) {
            $scope.results.push(store[item.ref]);
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

