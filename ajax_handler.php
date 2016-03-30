<?php
// load necessary files
include('loader.php');

// Session is necessary for this file
session_dasygenis();

//handles the post requests
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST["ajax_action"]) && isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){

	switch($_POST["ajax_action"]){

		// select users for add/edit editor list
		case "select_users_like":
			$possible_users = $db->find_users_like($_POST["username"]);
			$users_array = array();
			foreach($possible_users as $user){ 
				array_push($users_array,$user['username']);
			}
			header('Content-Type: application/json');
			echo json_encode($users_array);
		break;
			
		// select users/projects/libraries for navbar search
		case "search_navbar":
			if($_POST["type"] == "Users"){
				$possible_users = $db->find_users_like($_POST["query"]);
				$suggestions = array();
				foreach($possible_users as $user){ 
					array_push($suggestions,$user['username']);
				}
			}elseif($_POST["type"] == "Projects"){
				$possible_projects = $db->find_projects_like($_POST["query"]);
				$suggestions = array();
				foreach($possible_projects as $project){ 
					array_push($suggestions,$project['name']);
				}
				
			}else{
				$possible_libraries = $db->find_libraries_like($_POST["query"]);
				$suggestions = array();
				foreach($possible_libraries as $library){ 
					array_push($suggestions,$library['name']);
				}
			}
			header('Content-Type: application/json');
			echo json_encode($suggestions);
		break;
			
		// select projects by name to redirect after navbar search
		case "select_project_by_name":
			$suggestions = $db->find_project_for_link($_POST["name"]);
			header('Content-Type: application/json');
			echo json_encode($suggestions);
		break;
			
		// Show the filtered libraries
		case "filter_libraries":
			$name = $_POST["query"];
			$page = 0;
			require('pages/extra/list-all-libraries.php');
		break;
		
		// Generate if necessary and get the json information from requested vcd file
		case "read_vcd":
			$project_id = $_POST["project_id"];
			$project = $db->get_project($_POST['project_id']);
			$editors = $db->get_project_editors($project['id']);
			
			$path = $BASE_DIR.$_SESSION['vhdl_user']['username']."/".$project['short_code']."/";
			$vcd_file = $BASE_DIR.$_SESSION['vhdl_user']['username']."/".$project['short_code']."/".$_POST["vcd_name"];;
			$data = array();
			$module = "";
			$time_interval_couter=0;
			$changes_at_time=0;
			$timescale='s';
			$vcd_timestamp = $path.filemtime($vcd_file);
			$data_array='';
			
			if( $user->validate_ownership($editors) ){
				if(file_exists($vcd_timestamp.'.json') ){
					header('Content-Type: application/json');
					echo file_get_contents($vcd_timestamp.'.json');
					exit();
				}else{
					$handle = fopen($vcd_file, "r");
					if($handle){
						while (($line = fgets($handle)) !== false) {
							// if the line is timescale definition, keep it to the time information
							if(  preg_match('/^  1 .*/',$line) === 1){
								// trim unnecessary parts and keep the timescale units
								$timescale = trim(str_replace('1',"",$line));
							}
							
							// if the line is module definition, add that module to the path
							if(  preg_match('/^\$scope module .*/',$line) === 1){
								// trim unnecessary parts and keep the module's name
								$line = str_replace('$scope module ',"",$line);
								$module .= "_-_".trim(str_replace(' $end',"",$line));
							}
							
							// if the line is module ending, remove the last module from the path
							if(  preg_match('/^\$upscope \$end/',$line) === 1){
								// get the index of the last slash "/"
								$pos = strrpos($module, "_-_");
								// get the substring from index 0 to $pos-1
								$module = substr($module, 0, $pos );
							}
							
							// if the line defines a variable
							if( preg_match('/^\$var (reg|wire) .*/',$line,$match) === 1){
							
								// trim the unnecessary parts
								$line=str_replace(' $end',"",$line);
								$line=str_replace('$var '.$match[1].' ',"",$line);
								// and split the line to the var's length,
								//  the var's symbol and it's name
								$str_array=explode(" ",$line);
								$data[$str_array[1]] = array(
									"name" => trim($str_array[2]),
									"length" => trim($str_array[0]),
									"module" => $module
								);
							}
							
							// if the line is time for value changes
							if(  preg_match('/^#\d+/',$line) === 1){
								// keep the time of the changes
								$changes_at_time = trim(str_replace('#',"",$line));					
								$time_interval_couter++;
							}
							
							// if the line is a variable change value
							if( preg_match('/^\d.*/',$line) === 1 || preg_match('/^U.*/',$line) === 1 ){
								// save the new value in the array
								$data[trim(substr($line,1))][$changes_at_time]=trim($line[0]);

							}
							if( preg_match('/^bU.*/',$line) === 1 || preg_match('/^b\d.*/',$line) === 1 ){

								// get the change value and it's symbol
								$str_array=explode(" ",$line);
								
								// save the new value in the array
								$data[trim($str_array[1])][$changes_at_time]=trim($str_array[0]);

							}
						}
					}else{
						echo "Could not open file";
					} 
					fclose($handle);
					
					$data['time_info']= array(
						"duration" => $changes_at_time,
						"intervals" => $time_interval_couter,
						"timescale" => $timescale
					);
					
					$fp = fopen($vcd_timestamp.'.json', 'w') or die("Unable to open file!");
					fwrite($fp, json_encode($data));
					fclose($fp);
					$data_array = $data;
				}
			}
			header('Content-Type: application/json');
			echo json_encode($data_array);
		break;
	}
}else{
	header("Location:".$BASE_URL);
}