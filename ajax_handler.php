<?php
// load necessary files
include('loader.php');
// Session is necessary for this file
session_dasygenis();

//handles the post requests
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST["ajax_action"])){

	switch($_POST["ajax_action"]){

		// confirm log in information (id > 0){if given)
		case "select_users_like":
			$possible_users = $db->find_users_like($_POST["username"]);
			$users_array = array();
			foreach($possible_users as $user){ 
				array_push($users_array,$user['username']);
			}
			header('Content-Type: application/json');
			echo json_encode($users_array);
		break;
	}
}else{
	header("Location:".$BASE_URL);
}