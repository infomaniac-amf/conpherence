var Event = require('./models/Event');
var Session = require('./models/Session');
var Speaker = require('./models/Speaker');
var XHR = require('./xhr');

var HomeCtrl = require('./controllers/home');
var SessionCtrl = require('./controllers/session');

var app = angular.module('conpherence', ['ui.router'])
    .config(function ($stateProvider, $urlRouterProvider) {
        $urlRouterProvider.otherwise("/home");

        $stateProvider
            .state('home', {
                url: "/home",
                templateUrl: "pages/home.tmpl",
                controller: HomeCtrl
            })
            .state('sessions', {
                url: "/sessions/:event",
                templateUrl: "pages/sessions.tmpl",
                controller: SessionCtrl
            })
    })
    .controller('AppCtrl', function ($scope, $element) {

    });