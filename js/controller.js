var craigslistApp = angular.module('craigslist', []);

craigslistApp
	.factory('craigslistService', function($http){
		return {
			getCities: function(){
				return $http.get('/api/getCities')
					.then(function(result){
						return result.data;
					});
			},
			search: function(query, city){
				return $http({
					url: '/api/search', 
					data: { query: query, city: city },
					method: 'POST',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'}
				})
				.then(function(result){
					return {
						city: city,
						listings: result.data
					}
				});
			}
		};
	});

craigslistApp.controller('CraigslistController', function($scope, craigslistService){
	$scope.query = '';
	$scope.cities = [];
	$scope.listings = [];
	var searchLock = false;
	
	craigslistService.getCities().then(function(result){
		$scope.cities = result;
	});
	
	
	$scope.search = function(){
		search(0);
		/*
		var results = craigslistService.search($scope.query, $scope.cities[0]).then(function(result){
			$scope.listings.push(result);
		});
		*/
	}
	
	$scope.stop = function(){
		searchLock = true;
	}
	
	
	var search = function(index){
		if (searchLock) return;
		craigslistService
			.search($scope.query, $scope.cities[index])
			.then(function(result){
				$scope.listings.push(result);
				search(++index);
			});
	}
	
});
