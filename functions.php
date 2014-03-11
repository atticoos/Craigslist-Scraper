<?php
	include 'simple_html_dom.php';
	
	
	
	
	if ($_POST['action'] == 'searchCity'){
		searchCity($_POST['city'], $_POST['query']);
	}
	
	
	function searchCity($city, $query){
		
		$query = "http://$city.craigslist.com/search/web?query=$query";
		$html = file_get_html($query);
		$rows = $html->find('.row');

		foreach($rows as $row){
			echo $row;
		}
		exit(0);
	}
	
	
	
	function getCities() {
		$html = file_get_html('http://www.craigslist.com');
		
		$container = $html->find('.colmask');
		$html = str_get_html($container[0]);
		$lists = $html->find('a');
		
		foreach($lists as $i=>$link){
			$output = $link->href;
			$output = str_replace("http://", "", $output);
			$output = str_replace(".craigslist.org", "", $output);
			$output = trim($output);
			echo $output;
			if ($i < count($lists)-1){
				echo ",";
			}
		}
	}
	
