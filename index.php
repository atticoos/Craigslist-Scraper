<?php
	$cities = file_get_contents('cities.txt');
	$cities = explode(",", $cities);
?>
<!DOCTYPE html>
<html ng-app="craigslist">
	<head>
		<title>Scraper</title>
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/angularjs/1.2.14/angular.min.js"></script>
		<script type="text/javascript" src="/js/service.js"></script>
		<script type="text/javascript" src="/js/controller.js"></script>
		<!-- Latest compiled and minified CSS -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
		
		<!-- Optional theme -->
		<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
		
	</head>
	<body ng-controller="CraigslistController">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
					<h2>CL Scraper</h2>
					
					
					<div class="panel panel-default">
						<div class="panel-body">
												
							<form class="form-inline" role="form">
								<div class="form-group">
									<input type="text" id="query" class="form-control" placeholder="Search term" ng-model="query" /> 
								</div>
								<div class="form-group">
									<input type="button" id="search" class="btn btn-default" value="Search" ng-click="search()" />
								</div>
								<div class="form-group">
									<input type="button" id="stop" class="btn btn-danger" value="Stop" ng-click="stop()" />
								</div>
							</form>
							<br/>
							Searching <span id="city-index" ng-bind="listings.length"></span> of <span id="city-total" ng-bind="cities.length">0</span> cities.
							
							<div class="progress">
							  <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: {{ (listings.length/cities.length) * 100 }}%;">
							    <span class="sr-only"></span>
							  </div>
							</div>
							
						</div>
					</div>
					
					
							
				
				
				
					
					<div class="panel panel-default">
						<div class="panel-heading">
							<h3 class="panel-title">Results</h3>
						</div>
						<div class="panel-body">
						
							<div ng-repeat="group in listings">
								<h3 ng-bind="group.city"></h3>
								
								<table class="table">
									<thead>
										<tr>
											<th>Date</th>
											<th>Listing</th>
										</tr>
									</thead>
									<tbody>
										<tr ng-repeat="listing in group.listings">
											<td ng-bind="listing.date"></td>
											<td><a href="{{listing.link.href}}">{{listing.link.title}}</td>
										</tr>
									</tboy>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>

			
			
			
		
		</div>
		
		<?php if (false): ?>
		<script type="text/javascript">
			(function($){
				
				window.Scraper = function(){
					var self = this;
					var cities = <?php echo json_encode($cities); ?>;
					var currentCity = 0;
					
					
					this.renderResults = function(results, index){
					
						
					
					
					
						$("#city-index").html(index);
						$("#results").append(results);
					}
					
					this.search = function(query, index){
						console.log("Searching", index);
						if (!index){
							index = 0;
						}
						if (index > cities.length || index == 5){
							return;
						}
						$.post('functions.php', {
							action: 'searchCity',
							query: query,
							city: cities[index]
						}, function(results){
							self.renderResults(results, index+1);
							self.search(query, ++index);
						});
					}
					
					this.searchClick = function(){
						$("#city-index").html(0);
						self.search($("#query").val());
					};
					this.init = function(){
						$("#city-total").html(cities.length);
					};
				};
				
				$(document).ready(function(){
					var scraper = new window.Scraper();
					scraper.init();
					
					$("#search").click(scraper.searchClick);
				});
				
			})(jQuery);
		</script>
		<?php endif; ?>
		
	</body>
</html>