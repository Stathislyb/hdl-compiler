<?php
// load necessary files
include('loader.php');
// Session is necessary for this file
session_dasygenis();

//handles the post requests
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST["ajax_action"])){

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
	}
}else{
	header("Location:".$BASE_URL);
}