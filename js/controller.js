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
			getStates: function(){
				return $http.get('/api/getStates')
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
					return result.data;
				});
			}
		};
	});

craigslistApp.controller('CraigslistController', function($scope, craigslistService){
	$scope.query = '';
	$scope.cities = [];
	$scope.states = [];
	$scope.listings = [];
	$scope.views = { filterStates: false }
	var searchLock = false;
	
	craigslistService.getCities().then(function(result){
		$scope.cities = result;
	});
	
	craigslistService.getStates().then(function(result){
		$scope.states = result;
	});
	
	
	$scope.search = function(){
		search();
		/*
		var results = craigslistService.search($scope.query, $scope.cities[0]).then(function(result){
			$scope.listings.push(result);
		});
		*/
	}
	
	$scope.stop = function(){
		searchLock = true;
	}
	
	
	var search = function(stateIndex, cityIndex){
		
		if (!stateIndex) stateIndex = 0;
		if (!cityIndex) cityIndex = 0;
		if (searchLock || stateIndex == $scope.states.length) return;

		craigslistService
			.search($scope.query, $scope.states[stateIndex].cities[cityIndex].key)
			.then(function(result){
				$scope.listings.push({
					state: $scope.states[stateIndex].state,
					city: $scope.states[stateIndex].cities[cityIndex].name,
					listings: result
				});
				if (cityIndex == $scope.states[stateIndex].cities.length - 1){
					return search(++stateIndex);
				}
				search(stateIndex, ++cityIndex);
			});
	}
	
});
