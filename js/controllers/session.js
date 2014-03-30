var XHR = require('./../xhr');

module.exports = function ($scope, $rootScope, $state) {
    if(!$rootScope.selectedEvent) {
        $state.go('home'); // you're drunk
    }

    $scope.event = $rootScope.selectedEvent;
};