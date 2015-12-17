<?php
// Session is necessary for this file
session_dasygenis();

//handles the post requests
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST["post_action"])){

	switch($_POST["post_action"]){

		// confirm log in information (id > 0){if given)
		case "login":
			$id = $db->confirm_user($_POST["username"],$_POST["password"]);
			if($id > 0){
				$_SESSION['vhdl_user']['username'] = $_POST["username"];
				$_SESSION['vhdl_user']['id'] = $id;
				$_SESSION['vhdl_user']['loged_in'] = 1;
				array_push($_SESSION['vhdl_msg'],"success_login");
			}else{
				array_push($_SESSION['vhdl_msg'],"fail_login");
			}
		break;

		// log user out (if requested)
		case "logout":
			unset($_SESSION['vhdl_user']);
			unset($_SESSION['PID']);
			setcookie("PID", 0, time()-3600);
		break;

		// register user
		case "register":
			if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && strlen($_POST["email"])<=50 ) {
				if($_POST["password"] == $_POST["password_confirm"]){
					if($db->register_user($_POST["username"],$_POST["password"],$_POST["email"])){
						array_push($_SESSION['vhdl_msg'], 'success_register');	
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_register');
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'fail_register_confirm');	
				}
			}else{
				array_push($_SESSION['vhdl_msg'], 'invalid_mail');
			}
		break;

		// Create Project
		case "create_project":
			$short_code = create_short_code($_POST['project_name']);
			$project_id = $db->create_project($_POST['project_name'], $_POST['project_description'], $short_code);
			if($project_id > 0){
				if( $db->add_project_editor($_SESSION['vhdl_user']['username'], $project_id, 1) >0 ){
					array_push($_SESSION['vhdl_msg'], 'success_project_creation');
					header("Location:".$BASE_URL."/project/".$_SESSION['vhdl_user']['username']."/".$short_code);
					exit();
				}else{
					array_push($_SESSION['vhdl_msg'], 'fail_project_creation');
				}
			}else{
				array_push($_SESSION['vhdl_msg'], 'fail_project_creation');
			}
		break;
			
		// Edit Project
		case "edit_project":
			$short_code = create_short_code($_POST['project_name']);
			if( $db->edit_project($_POST['project_name'], $_POST['project_description'], $short_code, $_POST['project_id']) ){
				//if( $db->add_project_editor($_SESSION['vhdl_user']['username'], $project_id, 1) >0 ){
					array_push($_SESSION['vhdl_msg'], 'success_project_edit');
					header("Location:".$BASE_URL."/edit-project/".$short_code);
					exit();
				//}else{
					//array_push($_SESSION['vhdl_msg'], 'fail_project_edit');
				//}
			}else{
				array_push($_SESSION['vhdl_msg'], 'fail_project_edit');
			}
		break;
	}
	
}
?>