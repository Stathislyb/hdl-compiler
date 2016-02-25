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
			$pid="0";
			setcookie("PID", 0, time()-3600);
			unset($_SESSION['vhdl_user']);
			unset($_SESSION['PID']);
		break;

		// register user
		case "register":
			if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && strlen($_POST["email"])<=50 ) {
				if($_POST["password"] == $_POST["password_confirm"]){
					if($db->register_user($_POST["username"],$_POST["password"],$_POST["email"])){
						array_push($_SESSION['vhdl_msg'], 'success_register');	
						mkdir($BASE_DIR.$_POST["username"],0700);
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
					mkdir($BASE_DIR.$_SESSION['vhdl_user']['username']."/".$short_code,0700);
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
			$project = $db->get_project($_POST['project_id']);
			$db->add_project_multi_editors($_POST['projet_authors'], $project['id']);
			
			if($db->edit_project($_POST['project_name'], $_POST['project_description'], $short_code, $_POST['project_id'], $_POST['project_share']) ){
				array_push($_SESSION['vhdl_msg'], 'success_project_edit');
				$old_name = $BASE_DIR.$_SESSION['vhdl_user']['username']."/".$project['short_code'];
				$new_name = $BASE_DIR.$_SESSION['vhdl_user']['username']."/".$short_code;
				rename($old_name, $new_name);
				header("Location:".$BASE_URL."/edit-project/".$short_code);
				exit();
			}else{
				array_push($_SESSION['vhdl_msg'], 'fail_project_edit');
			}
		break;
			
		// Create Directory
		case "Create_dir":
			$name = create_short_code($_POST['dirfile_name']);
			$path = $BASE_DIR.$_POST['owner']."/".$_POST['project_shortcode'].$_POST['current_dir']."/".$name;
			$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
			$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);
			if(!$file_exists){
				if( mkdir($path,0700)){
					if($db->add_dir_file($name, $project['id'], "directory", $_POST['current_dir']) > 0){
						array_push($_SESSION['vhdl_msg'], 'success_create_dir');
						header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']."/directory/".$_POST['current_dir']);
						exit();
					}
				}
				rmdir($path);
			}
			array_push($_SESSION['vhdl_msg'], 'fail_create_dir');
		break;
				
		// Create File
		case "Create_file":
			$name = create_short_code($_POST['dirfile_name']);
			$path = $BASE_DIR.$_POST['owner']."/".$_POST['project_shortcode'].$_POST['current_dir']."/".$name;
			$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
			$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);
			if(!$file_exists){
				if( fopen($path,"w+") && !$file_exists){				
					if($db->add_dir_file($name, $project['id'], "file", $_POST['current_dir']) > 0){
						array_push($_SESSION['vhdl_msg'], 'success_create_file');
						header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']."/directory/".$_POST['current_dir']);
						exit();
					}
				}
				rmdir($path);
			}
			array_push($_SESSION['vhdl_msg'], 'fail_create_file');
		break;
		
		case "set_sid":
			echo "new SID";
			$_SESSION['PID']=intval($_POST['pid']);
			setcookie("PID", $_SESSION['PID'], time()+3600);
		break;
			
		case "new_sid":
			srand();
			$number=rand()%100000;
			while(!mkdir($BASE.$number,0700)){
				$number=rand()%100000;
			}
			// Ok we found a new random file
			$_SESSION['PID']=$number;
			setcookie("PID", $_SESSION['PID'], time()+3600);
		break;
			
	}
	
}
?>