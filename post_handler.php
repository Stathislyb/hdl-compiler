<?php
// Session is necessary for this file
session_dasygenis();

//handles the post requests
if($_SERVER['REQUEST_METHOD']=='POST' && isset($_POST["post_action"]) ){
	
	if( isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){
		
		switch($_POST["post_action"]){
				
			// log user out (if requested)
			case "logout":
				$pid="0";
				unset($_SESSION['vhdl_user']);
				unset($_SESSION['SID']);
				unset($user);
			break;
			
			// log user out (if requested)
			case "activate":
				$result= $db->activate_user($_SESSION['vhdl_user']['id'],$_POST['code']);
				if($result){
					$_SESSION['vhdl_user']['activated']=1;
					array_push($_SESSION['vhdl_msg'], 'activation_success');
				}else{
					array_push($_SESSION['vhdl_msg'], 'activation_fail');
				}
			break;

			// Create Project
			case "create_project":
				$short_code = $gen->create_short_code($_POST['project_name']);

				if( strlen($short_code) < 5 ){
					array_push($_SESSION['vhdl_msg'], 'invalid_project_name');
				}else{
					$project_id = $db->create_project($_POST['project_name'], $_POST['project_description'], $short_code, $_POST['project_share']);

					if($project_id > 0){
						if( $db->add_project_editor($_SESSION['vhdl_user']['username'], $project_id, 1) >0 ){
							if(!empty($_POST['projet_authors'])){
								$db->add_project_multi_editors($_POST['projet_authors'], $project_id);
							}
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
				}
			break;

			// Edit Project
			case "edit_project":
				$short_code = $gen->create_short_code($_POST['project_name']);
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				
				if( $user->validate_ownership($editors) ){
					if( strlen($short_code) < 5 ){
						array_push($_SESSION['vhdl_msg'], 'invalid_project_name');
					}else{
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
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			// Remove Project
			case "Remove_Project":
				$_POST['project_id'];
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				$full_path = $BASE_DIR.$_SESSION['vhdl_user']['username'].'/'.$project['short_code'];
				if( $user->validate_ownership($editors) ){
					$db->clear_project($project['id']);
					$db->remove_project($project['id']);
					if (file_exists($full_path)){
						system("rm -rf ".escapeshellarg($full_path));
					}
					array_push($_SESSION['vhdl_msg'], 'remove_project_success');
					header("Location:".$BASE_URL);
					exit();
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			// Create Directory
			case "Create_dir":
				$name = $gen->create_short_code($_POST['dir_name']);
				$path = $BASE_DIR.$_POST['owner']."/".$_POST['project_shortcode'].$_POST['current_dir']."/".$name;
				$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
				$editors = $db->get_project_editors($project['id']);
				$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);
				if( $user->validate_edit_rights($editors) ){
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
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			// Create File
			case "Create_file":
				$name = $gen->create_short_code($_POST['file_name']);
				if($_POST['current_dir']=="/"){
					$path = $BASE_DIR.$_POST['owner']."/".$_POST['project_shortcode']."/".$name;
				}else{
					$path = $BASE_DIR.$_POST['owner']."/".$_POST['project_shortcode'].$_POST['current_dir']."/".$name;
				}
				$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
				$editors = $db->get_project_editors($project['id']);
				$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);
				if( $user->validate_edit_rights($editors) ){
					if(!$file_exists){
						if( fopen($path,"w+") && !$file_exists){				
							if($db->add_dir_file($name, $project['id'], "file", $_POST['current_dir']) > 0){
								array_push($_SESSION['vhdl_msg'], 'success_create_file');
								header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']."/directory".$_POST['current_dir']);
								exit();
							}
						}
					}
					array_push($_SESSION['vhdl_msg'], 'fail_create_file');
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;
				
				// Upload File
			case "Upload_File":
				$path = $_POST['upload_dir'];
				$name = $gen->create_short_code(basename($_FILES['userfile']['name']));
				$uploadfile = $path . $name;
				$current_dir = $_POST['current_dir'];
				$project = $db->get_project_shortcode($_POST['project_shortcode'], $_SESSION['vhdl_user']['id']);
				$editors = $db->get_project_editors($project['id']);
				$file_exists = $db->check_file_dir_exist($name, $project['id'],  $_POST['current_dir']);

				if( $user->validate_edit_rights($editors) ){
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
					} 
					
					$file_id = $db->add_dir_file($name, $project['id'], "file", $current_dir);
					if($file_id > 0){		
						
						$ret=move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
						if ($ret) {
							
							$checkfile = pathinfo($uploadfile);
							if ( $checkfile['extension'] == "zip" || $checkfile['extension'] == "ZIP" ){

								if( $gen->extract_file($uploadfile,$path, $current_dir, $project['id']) ){
									array_push($_SESSION['vhdl_msg'], 'success_upload_file');
								}else{
									array_push($_SESSION['vhdl_msg'], 'fail_upload_file_unzip');
								}
								unlink($uploadfile);
								$db->remove_file($file_id);
							}else{
								array_push($_SESSION['vhdl_msg'], 'success_upload_file');
							}
							
						} else {
							array_push($_SESSION['vhdl_msg'], 'fail_upload_file'); 
						}
						
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_upload_file');
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			case "Compile_Selected":
				$selected = explode('-',$_POST['selected_ids']);
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				if( $user->validate_edit_rights($editors) ){
					foreach ($selected as $file_id) {
						$file = $db->get_file($file_id);
						if($file['type']=='file'){
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
								if( file_exists($full_path.".log") ){
									unlink($full_path.".log");
								}
								create_job_file($directory,$file['name'],$prefile,$postfile,$executable,$timeout);
								$db->file_compile_pending($file['id']);
								
								array_push($_SESSION['vhdl_msg'], 'compile_success');
							}else{
								array_push($_SESSION['vhdl_msg'], 'compile_fail');
							}
						}
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;
				
			case "Simulate_Project":
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				if( $user->validate_edit_rights($editors) ){
					$owner = $db->get_project_owner($project['id']);
					$directory = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/';
					$architectureclean=filter_var($_POST['architecture'],FILTER_SANITIZE_STRING);	
					$explodedarchitecture=explode(" ",$architectureclean);
					$unit=$explodedarchitecture[0];
					$architecture=$explodedarchitecture[1];	
					check_and_create_job_directory();
					
					$extra_pre = '';
					if ( isset($_POST['extralib'] ) ){
						if ( $_POST['extralib'] == "synopsys"){ 
							$extra_pre = " --ieee=synopsys ";
						}
					}
					$prefile="--elab-run ".$extra_pre;
					$extra_post = '';
					if ( isset($_POST['extrasim'] ) ){
						if ( $_POST['extrasim'] == "vcd"){ 
							$extra_post = " --vcd=testbench.vcd ";
						}
					}
					$postfile=$architecture.$extra_post;
					
					create_job_file($directory,$unit,$prefile,$postfile);
					array_push($_SESSION['vhdl_msg'], 'simulation_success');
					
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			case "Remove_Selected":
				$selected = explode('-',$_POST['selected_ids']);
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				if( $user->validate_edit_rights($editors) ){
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
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			case "Post_Library_Selected":
				$selected = explode('-',$_POST['selected_ids']);
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				if( $user->validate_edit_rights($editors) ){
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
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
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
				$editors = $db->get_project_editors($project['id']);
				if( $user->validate_edit_rights($editors) ){
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
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;
				
				// SID Create File
			case "SID_Create_file":
				$name = $gen->create_short_code($_POST['file_name']);
				$path = $BASE.$_SESSION['SID']."/".$name;
				
				if(fopen($path,"w+")){	
					if( $db->add_sid_file($name, $_SESSION['SID']) >0 ){
						array_push($_SESSION['vhdl_msg'], 'success_create_file');
						header("Location:".$BASE_URL);
						exit();
					}
				}
				array_push($_SESSION['vhdl_msg'], 'fail_create_file');
			break;
				
				// SID Upload File
			case "SID_Upload_File":
				$path = $BASE.$_SESSION['SID'];
				$name = $gen->create_short_code(basename($_FILES['userfile']['name']));
				$uploadfile = $path . $name;
				
					if(file_exists($path.$name)){
						array_push($_SESSION['vhdl_msg'], 'file_exists');
						header("Location:".$BASE_URL);
						exit();			
					}

					//Check for errors
					if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) { 
					//uploading successfully done 
					} else { 
						array_push($_SESSION['vhdl_msg'], 'fail_upload_file');
						header("Location:".$BASE_URL);
						exit();
					} 
					
					$file_id = $db->add_sid_file($name, $_SESSION['SID']);
					if($file_id > 0){		
						
						$ret=move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
						if ($ret) {
							
							$checkfile = pathinfo($uploadfile);
							if ( $checkfile['extension'] == "zip" || $checkfile['extension'] == "ZIP" ){

								if( $gen->extract_file($uploadfile,$path, $current_dir, $project['id']) ){
									array_push($_SESSION['vhdl_msg'], 'success_upload_file');
								}else{
									array_push($_SESSION['vhdl_msg'], 'fail_upload_file_unzip');
								}
								unlink($uploadfile);
								$db->remove_file($file_id);
							}else{
								array_push($_SESSION['vhdl_msg'], 'success_upload_file');
							}
							
						} else {
							array_push($_SESSION['vhdl_msg'], 'fail_upload_file'); 
						}
						
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_upload_file');
					}
			break;
						   
			case "SID_Compile_Selected":
				$selected = explode('-',$_POST['selected_ids']);
				foreach ($selected as $file_id) {
					$file = $db->get_file_sid($file_id);
					$full_path = $BASE.$_SESSION['SID'].'/'.$file['name'];
					$directory = $BASE.$_SESSION['SID'].'/';

					if (file_exists($full_path)){
						check_and_create_job_directory();
						$extra=process_pre_options($_POST);
						//$prefile="-a ".$extra;
						$prefile="-a ";
						$postfile="";
						$executable='/usr/lib/ghdl/bin/ghdl';
						$timeout=6;
						if( file_exists($full_path.".log") ){
							unlink($full_path.".log");
						}
						create_job_file($directory,$file['name'],$prefile,$postfile,$executable,$timeout);
						$db->file_compile_pending_sid($file['id']);

						array_push($_SESSION['vhdl_msg'], 'compile_success');
					}else{
						array_push($_SESSION['vhdl_msg'], 'compile_fail');
					}
				}
			break;
				
			case "SID_Simulate_Project":
				$directory = $BASE.$_SESSION['SID'].'/';
				$architectureclean=filter_var($_POST['architecture'],FILTER_SANITIZE_STRING);	
				$explodedarchitecture=explode(" ",$architectureclean);
				$unit=$explodedarchitecture[0];
				$architecture=$explodedarchitecture[1];	
				check_and_create_job_directory();

				$extra_pre = '';
				if ( isset($_POST['extralib'] ) ){
					if ( $_POST['extralib'] == "synopsys"){ 
						$extra_pre = " --ieee=synopsys ";
					}
				}
				$prefile="--elab-run ".$extra_pre;
				$extra_post = '';
				if ( isset($_POST['extrasim'] ) ){
					if ( $_POST['extrasim'] == "vcd"){ 
						$extra_post = " --vcd=testbench.vcd ";
					}
				}
				$postfile=$architecture.$extra_post;

				create_job_file($directory,$unit,$prefile,$postfile);
				array_push($_SESSION['vhdl_msg'], 'simulation_success');
				
			break;

			case "SID_Remove_Selected":
				$selected = explode('-',$_POST['selected_ids']);
				foreach ($selected as $file_id) {
					$file = $db->get_file_sid($file_id);
					$full_path = $BASE.$_SESSION['SID'].'/'.$file['name'];
					$result = true;

					if( !$db->remove_file_sid($file_id) ){
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
			
			// register user
			case "Edit_User":
				if(filter_var($_POST["email_edit"], FILTER_VALIDATE_EMAIL) && strlen($_POST["email_edit"])<=50 ) {
					if( $db->confirm_user($user->username,$_POST["password_edit"]) ){
						if( !empty($_POST["new_password_edit"]) && $_POST["new_password_edit"] == $_POST["rep_password_edit"]){
							$new_pass=$_POST["new_password_edit"];
						}else{
							$new_pass=NULL;
						}
						if(strlen($_POST["phone_edit"])!=10){
							$phone=NULL;
						}else{
							$phone=$_POST["phone_edit"];
						}
						if($db->edit_user($new_pass,$phone,$_POST["email_edit"],$_POST["ace_theme"],$user->id)){
							array_push($_SESSION['vhdl_msg'], 'success_edit_user');
						}else{
							array_push($_SESSION['vhdl_msg'], 'fail_edit_user');
						}
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_register_confirm');	
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'invalid_mail');
				}
			break;

		}
		
	}
	
	switch($_POST["post_action"]){

		// confirm log in information (id > 0){if given)
		case "login":
			$id = $db->confirm_user($_POST["username"],$_POST["password"]);
			if($id > 0){
				$_SESSION['vhdl_user']['username'] = $_POST["username"];
				$_SESSION['vhdl_user']['id'] = $id;
				$_SESSION['vhdl_user']['loged_in'] = 1;
				$_SESSION['vhdl_user']['activated'] = $db->is_user_active($id);
				$user = new User($_SESSION['vhdl_user']);
				array_push($_SESSION['vhdl_msg'],"success_login");
			}else{
				array_push($_SESSION['vhdl_msg'],"fail_login");
			}
		break;

		// register user
		case "register":
			if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && strlen($_POST["email"])<=50 ) {
				if($_POST["password"] == $_POST["password_confirm"]){
					if(strlen($_POST["telephone"])!=10){
						$phone=NULL;
					}else{
						$phone=$_POST["telephone"];
					}
					$code = $gen->generate_code();
					if($db->register_user($_POST["username"],$_POST["password"],$_POST["email"],$phone,$code)){
						if($phone!=NULL){
							$message="Your activation code is : ".$code;
							$gen->send_sms($message,$phone);
						}
						$subject = "HDL Everywhere Registration";
						$message = "Welcome to our website!\r\rYou, or someone using your email address, has completed registration at HDL Everywhere. You can complete registration by clicking the following link:\r http://snf-703457.vm.okeanos.grnet.gr/vhdl/ \rAnd after logging in, enter the Activation Code : ".$code." \r\r If this is an error, ignore this email and you will be removed from our mailing list.";
						$gen->send_email($message,$subject,$_POST["email"]);
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
		
		case "set_sid":
			$_SESSION['SID']=intval($_POST['pid']);
			$_SESSION['vhdl_user']['username'] = "Guest";
			$_SESSION['vhdl_user']['id'] = "0";
			$_SESSION['vhdl_user']['loged_in'] = 1;
			$_SESSION['vhdl_user']['activated'] = 1;
			$user = new User($_SESSION['vhdl_user']);
		break;
			
		case "new_sid":
			srand();
			$number=rand()%100000;
			while(!mkdir($BASE.$number,0700)){
				$number=rand()%100000;
			}
			// Ok we found a new random file
			$_SESSION['SID']=$number;
			$_SESSION['vhdl_user']['username'] = "Guest";
			$_SESSION['vhdl_user']['id'] = "0";
			$_SESSION['vhdl_user']['loged_in'] = 1;
			$_SESSION['vhdl_user']['activated'] = 1;
			$user = new User($_SESSION['vhdl_user']);
		break;
			
	}
}
?>