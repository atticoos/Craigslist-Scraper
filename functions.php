<?php
	include 'simple_html_dom.php';
	$_POST = json_decode(file_get_contents('php://input'), true);
	
	switch($_GET['action']){
		case 'search':
			searchCity($_POST['city'], $_POST['query']);
			break;
		case 'getCities':
			getCities();
			break;
			
		case 'getStates':
			getStates();
		
		exit(0);
	}
	
	
	function getStates(){
		$html = file_get_html('http://www.craigslist.com');
		
		$container = $html->find('.colmask');
		$html = str_get_html($container[0]);
		
		
		$states = array();
		$n=0;
		foreach($html->find('.box') as $column){

			foreach($column->children() as $i=>$child){
				if ($i%2==0){
					$states[$n] = array('state'	=> $child->plaintext, 'cities' => array() );
				} else {
					foreach($child->find('a') as $link){
						$states[$n]['cities'][] = array(
							'name'	=> $link->plaintext,
							'link'	=> $link->href,
							'key'	=> str_replace(".craigslist.org", "", str_replace("http://", "", $link->href))
						);
					}
					$n++;
				}
			}
		}
		echo json_encode($states);
	}
	
	
	
	function getCities(){
	
		$cities = file_get_contents('cities.txt');
		$cities = explode(",", $cities);
		echo json_encode($cities);
	}
	
	
	function searchCity($city, $query){
		
		$query = "http://$city.craigslist.com/search/web?query=$query";

		$html = file_get_html($query);
		$rows = $html->find('.row');
		
		$listings = array();
		
		foreach($rows as $row){
			$row = str_get_html($row);
			$link = $row->find('.pl a');
			$date = $row->find('.date');
			
			$listings[] = array(
				'link'	=> array(
					'href'	=> $link[0]->href,
					'title'	=> $link[0]->plaintext
				),
				'date'	=> $date[0]->plaintext
			);
		}
		
		echo json_encode($listings);
	}
	
	
	/*
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
	*/
