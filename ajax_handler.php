<?php
// load necessary files
include('loader.php');

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
		
		// save the selected theme to user preferences
		case "save_theme":
			$db->update_user_theme($_SESSION['vhdl_user']['id'],$_POST["theme"]);
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
		
		// Show the filtered users
		case "filter_users":
			$name = $_POST["query"];
			$page = 0;
			require('pages/extra/list-all-users_admin.php');
		break;
		// Show the filtered components
		case "filter_components":
			$name = $_POST["query"];
			$page = 0;
			require('pages/extra/list-all-components_admin.php');
		break;
		// Show the filtered projects
		case "filter_projects":
			$name = $_POST["query"];
			$page = 0;
			require('pages/extra/list-all-projects_admin.php');
		break;
		
		// save changes on file
		case "save_file":
			if($_POST['project_id'] == "SID"){
				$is_editor = true;
				$owner_used_space = filesize($BASE_SID.$_SESSION['SID']) / pow(1024,2);
				$owner_space = 20 ;
			}else{
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				$owner = $db->get_project_owner($project['id']);
				$is_editor = $user->validate_edit_rights($editors);
				$owner_used_space = filesize($BASE_DIR.$owner['username']) / pow(1024,2);
				$owner_space = $owner['available_space'];
			}
			
			if( $is_editor ){
				if($owner_used_space < $owner_space){
					if( isset($_POST['directory']) && isset($_POST['data'] ) ){
						$directory=$gen->filter_letters($_POST['directory']);
						$log_file = $directory.".log";
						$bin_file = str_replace(".vhdl", ".o", $directory);
						$contents=$_POST['data'];
						if (file_exists($directory)  && is_writable($directory) ){
							$ret=file_put_contents($directory,$contents);
							if(!$ret) { 
								error_get_last();
								echo "Failed to save the file.";
							}
							if( file_exists($log_file) ){
								unlink($log_file);
							}
							if( file_exists($bin_file) ){
								unlink($bin_file);
							}
							if($_SESSION['vhdl_user']['username'] == "Guest"){
								$db->file_recompile_prompt_sid($_POST['file_id']);
							}else{
								$db->file_recompile_prompt($_POST['file_id']);
							}
							echo "Changes saved.";
						}else{ //end if file exists
							echo "File does not exist.";
						}
					}
				}else{
					echo $messages->messages['space_fail'][0];
				}
			}
		break;
		
		// save changes on library
		case "save_library":			
			if( $user->type==1 ){
				if( isset($_POST['directory']) && isset($_POST['data'] ) ){
					$directory=$gen->filter_letters($_POST['directory']);
					$contents=$_POST['data'];
					if (file_exists($directory)  && is_writable($directory) ){
						$ret=file_put_contents($directory,$contents);
						if(!$ret) { 
							error_get_last();
							echo "Failed to save the file.";
						}
						echo "Changes saved.";
						$db->increase_library_version($_POST['library_id']);
					}else{ //end if file exists
						echo "File does not exist.";
					}
				}
			}
		break;
		
		// Generate if necessary and get the json information from requested vcd file
		case "read_vcd":
			$project = $db->get_project($_POST['project_id']);
			$editors = $db->get_project_editors($project['id']);
			$owner = $db->get_project_owner($_POST["project_id"]);
			
			$path = $BASE_DIR.$owner['username']."/".$project['short_code']."/";
			$vcd_file = $BASE_DIR.$owner['username']."/".$project['short_code']."/".$_POST["vcd_name"];
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
		
		// Generate if necessary and get the json information from requested SID vcd file
		case "read_vcd_sid":
			$path = $BASE.$_SESSION['SID']."/";
			$vcd_file = $path.$_POST["vcd_name"];;
			$data = array();
			$module = "";
			$time_interval_couter=0;
			$changes_at_time=0;
			$timescale='s';
			$vcd_timestamp = $path.filemtime($vcd_file);
			$data_array='';
			
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
			
			header('Content-Type: application/json');
			echo json_encode($data_array);
		break;
	}
}else{
	header("Location:".$BASE_URL);
}