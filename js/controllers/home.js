var XHR = require('./../xhr');

module.exports = function ($scope, $rootScope, $state) {
    $scope.events = [];

    XHR.getAMF('/amf/events', function (data) {
        $scope.events = data;

        $scope.showSessions(data[0]);
        $scope.$apply();
    });

    $scope.showSessions = function (event) {
        $rootScope.selectedEvent = event;
        $state.go('speakers');
    };
};