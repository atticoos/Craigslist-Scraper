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
			search: function(query, city, contractor){
				contractor = contractor ? 'true' : '';
				return $http({
					url: '/api/search', 
					data: { query: query, city: city, contractor:contractor },
					method: 'POST',
					headers: {'Content-Type': 'application/x-www-form-urlencoded'}
				})
				.then(function(result){
					return result.data;
				});
			}
		};
	})
	.filter('iff', function(){
		return function(input, trueVal, falseVal){
			return input? trueVal : falseVal;
		}
	});

craigslistApp.controller('CraigslistController', function($scope, craigslistService){
	$scope.query = '';
	$scope.contractor;
	$scope.cities = [];
	$scope.states = [];
	$scope.stateSelection = $scope.states;
	// state grouped listing
	$scope.listings = [];
	// listigs by date
	$scope.dateListings = [];
	
	$scope.views = { filterStates: false }
	$scope.listingFilter = 'date';
	$scope.searching = false;
	
	
	craigslistService.getStates().then(function(states){
		for (var i=0; i<states.length; i++){
			states[i].selected = true;
			for (var j=0; j<states[i].cities.length; j++){
				$scope.cities.push({
					state: states[i],
					city: states[i].cities[j]
				});
			}
		}
		$scope.states = states;
	});
	
	$scope.search = function(){
		$scope.listings 	= [];
		$scope.dateListings = [];
		$scope.searching 	= true;
		search();
	}
	
	var search = function(stateIndex, cityIndex){
		
		if (!stateIndex) stateIndex = 0;
		if (!cityIndex) cityIndex = 0;
		if (!$scope.searching || stateIndex == $scope.states.length-1) return;
		
		if (!$scope.states[stateIndex].selected){
			return search(++stateIndex);
		}
		
		craigslistService
			.search($scope.query, $scope.states[stateIndex].cities[cityIndex].key, $scope.contractor)
			.then(function(listings){
				$scope.listings.push({
					state: $scope.states[stateIndex].state,
					city: $scope.states[stateIndex].cities[cityIndex].name,
					listings: listings
				});
				// OPTIMIZE LATER - remove duplicate dataset and restructure original model for multiple view arrangements
				for (var i=0; i<listings.length; i++){
					$scope.dateListings.push({
						state: $scope.states[stateIndex].state,
						city: $scope.states[stateIndex].cities[cityIndex].name,
						link: listings[i].link,
						date: new Date(listings[i].date)
					});		
				}
				
				
				if (cityIndex == $scope.states[stateIndex].cities.length - 1){
					return search(++stateIndex);
				}
				return search(stateIndex, ++cityIndex);
			});
	}
	
	$scope.cityCountFilter = function(item){
		return item.state.selected;
	}
	
	$scope.stop = function(){
		$scope.searching = false;
	}
	
	$scope.toggleDateFilter = function(){
		$scope.listingFilter = 'date';
	}
	$scope.toggleStateFilter = function(){
		$scope.listingFilter = 'state';
	}
	var toggleStates = true;
	$scope.toggleAllStates = function(){
		toggleStates = !toggleStates;
		for (var i=0; i<$scope.states.length; i++){
			$scope.states[i].selected = toggleStates;
		}
	}
	
	
});
