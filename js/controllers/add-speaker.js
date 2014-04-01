var XHR = require('./../xhr');
var Speaker = require('./../models/Speaker');
var Image = require('./../models/Image');
var Session = require('./../models/Session');

module.exports = function ($scope, $rootScope, $state) {
    $scope.countries = ['...loading countries...'];

    $scope.speaker = new Speaker();
    $scope.sessions = [];

    $scope.readImage = function (input) {
        if (input.files && input.files[0]) {
            var fileReader = new FileReader();
            fileReader.onload = function (e) {

                $scope.speaker.image = new Image();
                $scope.speaker.image.data = new ByteArray(( e.target.result ));

            };
            fileReader.readAsBinaryString(input.files[0]);
        }
    };

    $scope.addSession = function() {
        $scope.sessions.push({});
    };

    $scope.getSessions = function(speaker) {
        var sessions = [];
        angular.forEach($scope.sessions, function(data) {
            var session = new Session({
                title: data.title,
                description: data.description,
                date: new Date(data.date),
                speaker: speaker
            });

            sessions.push(session);
        });

        return sessions;
    };

    $scope.openFileDialog = function ($event) {
        var fileInput = $($event.currentTarget).siblings('input[type="file"]');
        fileInput.change(function (e) {
            $scope.readImage(e.currentTarget);
        });
        fileInput.click();
    };

    $scope.addSpeaker = function() {
        $scope.speaker.sessions = $scope.getSessions($scope.speaker);

        XHR.postAMF('/amf/speakers', AMF.stringify($scope.speaker, AMF.CLASS_MAPPING), function (data) {
            alert($scope.speaker.name + ' created!');
            $state.go('home');
        });
    };

    XHR.getAMF('/amf/countries', function (data) {
        $scope.countries = data;
        $scope.$apply();
    });
};