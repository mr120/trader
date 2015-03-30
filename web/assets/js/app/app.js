var origin = document.location.origin;
var folder = document.location.pathname.split('/')[1];

var path = '/assets/templates/';

var myApp = angular.module('mainApp', ['ngRoute','googlechart']);

myApp.config(function($routeProvider, $locationProvider) {
    $routeProvider

        // route for the home page
        .when('/', {
            templateUrl : 'pages/home.html',
            controller  : 'tradeController'
        })

        // route for the about page
        .when('/pair/:from/:to', {
            templateUrl : 'pages/pair.html',
            controller  : 'pairController'
        });

    // use the HTML5 History API
    //$locationProvider.html5Mode(true);
});