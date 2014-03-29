var app = angular.module('conpherence', []);
app.controller('AppCtrl', function ($scope, $element) {
    get('/amf/speakers', function () {
        $scope.speakers = AMF.parse(this.responseText);
        $scope.$apply();
    });
});