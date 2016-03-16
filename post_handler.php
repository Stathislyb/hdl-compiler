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
			$short_code = $gen->create_short_code($_POST['project_name']);
			$project_id = $db->create_project($_POST['project_name'], $_POST['project_description'], $short_code, $_POST['project_share']);
			
			if($project_id > 0){
				if( $db->add_project_editor($_SESSION['vhdl_user']['username'], $project_id, 1) >0 ){
					$db->add_project_multi_editors($_POST['projet_authors'], $project_id);
					array_push($_SESSION['vhdl_msg'], 'success_project_creation');
					mkdir($BASE_DIR.$_SESSION['vhdl_user']['username']."/".$short_code,0777);
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
			$short_code = $gen->create_short_code($_POST['project_name']);
			$project = $db->get_project($_POST['project_id']);
			$db->add_project_multi_editors($_POST['projet_authors'], $project['id']);
			
			if($db->edit_project($_POST['project_name'], $_POST['project_description'], $short_code, $_POST['project_id'], $_POST['project_share']) ){
				array_push($_SESSION['vhdl_msg'], 'success_project_edit');
				$old_name = $BASE_DIR.$_SESSION['vhdl_user']['username']."/".$project['short_code'];
				$new_name = $BASE_DIR.$_SESSION['vhdl_user']['username']."/".$short_code;
				rename($old_name, $new_name);
				header("Location:".$BASE_URL."/project/".$_SESSION['vhdl_user']['username']."/".$short_code);
				exit();
			}else{
				array_push($_SESSION['vhdl_msg'], 'fail_project_edit');
			}
		break;
			
		// Remove Project
		case "Remove_Project":
			$_POST['project_id'];
			$project = $db->get_project($_POST['project_id']);
			$full_path = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'];
			
			$db->clear_project($project['id']);
			$db->remove_project($project['id']);
			if (file_exists($full_path)){
				system("rm -rf ".escapeshellarg($full_path));
			}
			
			header("Location:".$BASE_URL);
			exit();
		break;
			
		// Create Directory
		case "Create_dir":
			$name = $gen->create_short_code($_POST['dir_name']);
			$path = $BASE_DIR.$_POST['owner']."/".$_POST['project_shortcode'].$_POST['current_dir']."/".$name;
			$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
			$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);
			if(!$file_exists){
				if( mkdir($path,0700)){
					if($db->add_dir_file($name, $project['id'], "directory", $_POST['current_dir']) > 0){
						array_push($_SESSION['vhdl_msg'], 'success_create_dir');
						header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']."/directory".$_POST['current_dir']);
						exit();
					}
				}
				rmdir($path);
			}
			array_push($_SESSION['vhdl_msg'], 'fail_create_dir');
		break;
				
		// Create File
		case "Create_file":
			$name = $gen->create_short_code($_POST['file_name']);
			$path = $BASE_DIR.$_POST['owner']."/".$_POST['project_shortcode'].$_POST['current_dir']."/".$name;
			$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
			$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);
			if(!$file_exists){
				if( fopen($path,"w+") && !$file_exists){				
					if($db->add_dir_file($name, $project['id'], "file", $_POST['current_dir']) > 0){
						array_push($_SESSION['vhdl_msg'], 'success_create_file');
						header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']."/directory".$_POST['current_dir']);
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
			
			// Upload File
		case "Upload_File":
			$path = $_POST['upload_dir'];
			$name = $gen->create_short_code(basename($_FILES['userfile']['name']));
			$uploadfile = $path . $name;
			$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
			$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);
			
			if($file_exists){
				array_push($_SESSION['vhdl_msg'], 'file_exists');
				header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']."/directory".$_POST['current_dir']);
				exit();			
			}
			
			//Check for errors
			if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) { 
			//uploading successfully done 
			} else { 
				array_push($_SESSION['vhdl_msg'], 'fail_upload_file');
				header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']."/directory/".$_POST['current_dir']);
				exit();
				//throw new UploadException($_FILES['userfile']['error']); 
			} 

			if($db->add_dir_file($name, $project['id'], "file", $_POST['current_dir']) > 0){				
				$ret=move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
				if ($ret) {
					array_push($_SESSION['vhdl_msg'], 'success_upload_file');
				} else {
					//print_r(error_get_last());
					//echo "Upload failed. ErrorCode:".$ret;
					array_push($_SESSION['vhdl_msg'], 'fail_upload_file'); 
				}

				$checkfile = pathinfo($uploadfile);
				if ( $checkfile['extension'] == "zip" || $checkfile['extension'] == "ZIP" ){
					//Check to see if this is a zip file. It it is a zipfile then unzip it
					$ret=unzip_file_to_directory($uploadfile,$uploaddir);
					if ($ret==0){
						//file uploaded, just delete it
						unlink($uploadfile);
					}elseif ($ret==1){
						//file could not be uploaded
						//echo "Error in Unzipping";
						array_push($_SESSION['vhdl_msg'], 'fail_upload_file_unzip'); 
					}
				}
			}else{
				array_push($_SESSION['vhdl_msg'], 'fail_upload_file');
			}
		break;
			
		case "Compile_Selected":
			$selected = explode('-',$_POST['selected_ids']);
			$project = $db->get_project($_POST['project_id']);
			foreach ($selected as $file_id) {
				$file = $db->get_file($file_id);
				if($file['relative_path']=='/'){
					$full_path = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].'/'.$file['name'];
					$directory = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].'/';
				}else{
					$full_path = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].$file['relative_path'].'/'.$file['name'];
					$directory = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].$file['relative_path'].'/';
				}
				
				if (file_exists($full_path)){
					check_and_create_job_directory();
					$extra=process_pre_options($_POST);
					//$prefile="-a ".$extra;
					$prefile="-a ";
					$postfile="";
					$executable='/usr/lib/ghdl/bin/ghdl';
					$timeout=6;
					create_job_file($directory,$file['name'],$prefile,$postfile,$executable,$timeout);

					echo "job created";
				}else{
					echo "file not found at ".$full_path;
				}
			}
		break;
			
		case "Remove_Selected":
			$selected = explode('-',$_POST['selected_ids']);
			$project = $db->get_project($_POST['project_id']);
			foreach ($selected as $file_id) {
				$file = $db->get_file($file_id);
				if($file['relative_path']=='/'){
					$full_path = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].'/'.$file['name'];
				}else{
					$full_path = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].$file['relative_path'].'/'.$file['name'];
				}
				
				$result = true;
				
				if( is_dir($full_path) ){
					$db->remove_inner_files($file_id);
				}
				if( !$db->remove_file($file_id) ){
					$result=false;
				}
				if (file_exists($full_path)){
					system("rm -rf ".escapeshellarg($full_path));
				}else{
					$result=false;
				}
				
				if($result){
					array_push($_SESSION['vhdl_msg'], 'file_removed_success');
				}else{
					array_push($_SESSION['vhdl_msg'], 'file_removed_fail');
				}
			}
		break;
			
		case "Post_Library_Selected":
			$selected = explode('-',$_POST['selected_ids']);
			$project = $db->get_project($_POST['project_id']);
			foreach ($selected as $file_id) {
				$file = $db->get_file($file_id);
				if($file['relative_path']=='/'){
					$full_path = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].'/'.$file['name'];
				}else{
					$full_path = $BASE_DIR.$_POST['owner'].'/'.$project['short_code'].$file['relative_path'].'/'.$file['name'];
				}
				
				if(!is_dir($full_path) && !$db->check_lib_exist($file['name']) ){
					$result=true;
					if( !($db->add_library($file['name'],$_POST['owner']) > 0) ){
						$result=false;
					}		
					
					if (file_exists($full_path)){
						if( !copy($full_path,$BASE_DIR."libraries/".$file['name']) ){
							$result=false;
						}
					}else{
						$result=false;
					}
					
					if($result){
						array_push($_SESSION['vhdl_msg'], 'add_library_success');
					}else{
						array_push($_SESSION['vhdl_msg'], 'add_library_fail');
					}
					
				}else{
					array_push($_SESSION['vhdl_msg'], 'add_library_fail');
				}
			}
		break;
			
		case "Import_Library":
			$project = $db->get_project($_POST['project_id']);
			$library = $_POST['library'];
			$owner = $db->get_project_owner($project['id']);
			
			$full_path = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/libraries/'.$library;
			$libs_path = $BASE_DIR.'libraries/'.$library;
			$directory = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/libraries';
			
			$result=true;
			if(!is_dir($directory)){
				mkdir($directory,0777);
				$db->add_dir_file("libraries", $project['id'], "directory", "/");
			}
			
			if($db->check_file_dir_exist($library, $project['id'],  "/libraries")){
				array_push($_SESSION['vhdl_msg'], 'import_library_fail');
			}else{
				if( copy($libs_path, $full_path) ){
					chmod($full_path, fileperms($libs_path));
				}else{
					$result=false;
				}

				if($result){
					if($db->add_dir_file($library, $project['id'], "file", "/libraries") > 0){
						array_push($_SESSION['vhdl_msg'], 'import_library_success');
						header("Location:".$BASE_URL."/project/".$owner['username']."/".$project['short_code']."/directory/libraries");
						exit();
					}else{
						array_push($_SESSION['vhdl_msg'], 'import_library_fail');
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'import_library_fail');
				}
			}
		break;
			
	}
}
?>