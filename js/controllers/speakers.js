var XHR = require('./../xhr');

module.exports = function ($scope, $rootScope, $state, $sce) {

    if(!$rootScope.selectedEvent) {
        $state.go('home'); // you're drunk
        return;
    }

    $scope.event = $rootScope.selectedEvent;

    $scope.viewSessions = function(speaker) {
        speaker.sessionsVisible = !speaker.sessionsVisible;
    };

    $scope.getTrustedHTMLContent = function(content) {
        return $sce.trustAsHtml(content);
    };

    $scope.getDate = function(date) {
        return moment(date).format("ddd, hA");
    };
};