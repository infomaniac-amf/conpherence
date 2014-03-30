var XHR = require('./../xhr');

module.exports = function ($scope, $rootScope, $state) {

    if(!$rootScope.selectedEvent) {
        $state.go('home'); // you're drunk
        return;
    }

    $scope.event = $rootScope.selectedEvent;

    $scope.viewSessions = function(speaker) {
        speaker.sessionsVisible = !speaker.sessionsVisible;
    };

    $scope.getDate = function(date) {
        return moment(date).format("ddd, hA");
    };
};