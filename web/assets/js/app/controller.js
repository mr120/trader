'use strict';

myApp.controller('tradeController', ['$scope', '$http', '$location', function($scope, $http, $location) {
    $http.get('/api/v1/trade/get')
        .success(function (data) {
            $scope.messages = data;
        })
        .error(function (data) {
            //console.log('failed to get messages');
        });

    $scope.go = function ( path ) {
        $location.path( path );
    };

    $scope.formData = {};
    $scope.formData['currencyFrom'] = 'EUR';
    $scope.formData['currencyTo'] = 'GBP';


    $scope.processForm = function(e) {
        e.preventDefault();

        var postPromise = $http.post('/post/message',$scope.formData);

        postPromise.success(function(data, status, headers, config){
            if(status == 201) {
                alert('success');
            } else {
                alert('fail');
            }

        });
    };

}]);

myApp.controller('pairController', ['$scope', '$http', '$location', '$routeParams', function($scope, $http, $location, $routeParams) {
    $scope.messages = null;
    $scope.from = $routeParams.from;
    $scope.to = $routeParams.to;

    $http.get('/api/v1/trade/get/' + $routeParams.from + '/' + $routeParams.to)
        .success(function (data) {
            $scope.messages = data;
        })
        .error(function (data) {
            //console.log('failed to get messages');
        });

    $http.get('/api/v1/trade/get/stats/' + $routeParams.from + '/' + $routeParams.to)
        .success(function (data) {
            //console.log(data);
            var arr = [];
            $(data).each(function(key, value){

                var arr2 =
                    {"c": [
                        {"v": value.dateAdded.date},
                        {"v": value.high},
                        {"v": value.open},
                        {"v": value.close},
                        {"v": value.low}
                    ]};

                arr.push(arr2);
            });
            $scope.chartData = arr;
            loadChart();
        })
        .error(function (data) {
            console.log('failed to get messages');
        });

    function loadChart() {
        var chart1 = {};
        chart1.type = "CandlestickChart";
        chart1.data = {"cols": [
            {id: "month", label: "", type: "string"},
            {id: "buyLow", label: "", type: "number"},
            {id: "open", label: "Open", type: "number"},
            {id: "close", label: "Close", type: "number"},
            {id: "maxBuy", label: "High", type: "number"}
        ], "rows": $scope.chartData };

        chart1.options = {
            "isStacked": "false",
            "fill": 10,
            "displayExactValues": true,
            "vAxis": {
                "title": "Price", "gridlines": {"count": 6}
            },
            "hAxis": {
                "title": "Date", "gridlines": {"count": 6}
            },
            "legend": "none"
        };

        chart1.formatters = {};

        $scope.chart = chart1;
    }



}]);