var app = angular.module('conpherence', []);
app.controller('AppCtrl', function ($scope) {

    $scope.hello = "hi";

    get('/amf/speakers', function () {
        $scope.speakers = AMF.parse(this.responseText);
        console.table($scope.speakers);

        $scope.$apply();
    });
});