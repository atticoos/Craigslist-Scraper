<?php
	$cities = file_get_contents('cities.txt');
	$cities = explode(",", $cities);

?>
<html>
	<head>
		<title>Scraper</title>
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
	</head>
	<body>
		
		<h2>CL Scraper</h2>
		
		<input type="text" id="query" placeholder="Search term" /> 
		<input type="button" id="search" value="Search" />
		
		<div id="status">
			Searching <span id="city-index">0</span> of <span id="city-total">0</span> cities.
		</div>
	
	
		<div id="results">

		</div>
		
		
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
		
		
	</body>
</html>