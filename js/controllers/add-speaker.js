var XHR = require('./../xhr');
var Speaker = require('./../models/Speaker');
var Image = require('./../models/Image');

module.exports = function ($scope, $rootScope, $state) {
    $scope.countries = ['...loading countries...'];

    $scope.speaker = new Speaker();
    $scope.speaker.image = new Image();

    $scope.readImage = function (input) {
        if (input.files && input.files[0]) {
            var fileReader = new FileReader();
            fileReader.onload = function (e) {
                $scope.speaker.image.data = new ByteArray(( e.target.result ));
            };
            fileReader.readAsBinaryString(input.files[0]);
        }
    };

    $scope.openFileDialog = function ($event) {
        var fileInput = $($event.currentTarget).siblings('input[type="file"]');
        fileInput.change(function (e) {
            $scope.readImage(e.currentTarget);
        });
        fileInput.click();
    };

    $scope.addSpeaker = function() {
        XHR.postAMF('/amf/speakers', AMF.stringify($scope.speaker, AMF.CLASS_MAPPING), function (data) {
            debugger;
        });
    };

    XHR.getAMF('/amf/countries', function (data) {
        $scope.countries = data;
        $scope.$apply();
    });
};