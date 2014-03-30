var Event = require('./models/Event');
var Session = require('./models/Session');
var Speaker = require('./models/Speaker');
var XHR = require('./xhr');

var HomeCtrl = require('./controllers/home');
var SpeakerCtrl = require('./controllers/speakers');

var app = angular.module('conpherence', ['ui.router'])
    .config(function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/home");

        $stateProvider
            .state('home', {
                url: "/home",
                templateUrl: "pages/home.tmpl",
                controller: HomeCtrl
            })
            .state('speakers', {
                url: "/speakers",
                templateUrl: "pages/speakers.tmpl",
                controller: SpeakerCtrl
            })
    })
    .controller('AppCtrl', function ($scope, $element) {
    });