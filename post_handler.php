<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php

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
				$username = $user->username;
				if($user->type=='1'){
					if( isset($_POST['original_user']) && !empty($_POST['original_user']) ){
						$username = $_POST['original_user'];
					}
				}
				if( strlen($short_code) < 5 ){
					array_push($_SESSION['vhdl_msg'], 'invalid_project_name');
				}else{
					$project_id = $db->create_project($_POST['project_name'], $_POST['project_description'], $short_code, $_POST['project_share']);

					if($project_id > 0){
						if( $db->add_project_editor($username, $project_id, 1) >0 ){
							if(!empty($_POST['projet_authors'])){
								$db->add_project_multi_editors($_POST['projet_authors'], $project_id);
							}
							array_push($_SESSION['vhdl_msg'], 'success_project_creation');
							mkdir($BASE_DIR.$username."/".$short_code,0777);
							header("Location:".$BASE_URL."/project/".$username."/".$short_code);
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
				$owner = $db->get_project_owner($project['id']);
				
				if( $user->validate_ownership($editors) ){
					if( strlen($short_code) < 5 ){
						array_push($_SESSION['vhdl_msg'], 'invalid_project_name');
					}else{
						$db->add_project_multi_editors($_POST['projet_authors'], $project['id']);
						if($db->edit_project($_POST['project_name'], $_POST['project_description'], $short_code, $_POST['project_id'], $_POST['project_share']) ){
							array_push($_SESSION['vhdl_msg'], 'success_project_edit');
							$old_name = $BASE_DIR.$owner['username']."/".$project['short_code'];
							$new_name = $BASE_DIR.$owner['username']."/".$short_code;
							rename($old_name, $new_name);
							header("Location:".$BASE_URL."/project/".$owner['username']."/".$short_code);
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
			
			// Download Project
			case "Download_Project":
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				$file_types = array();
				if(isset($_POST['download_vhdl']) && $_POST['download_vhdl']=='on') array_push($file_types,".vhdl");
				if(isset($_POST['download_vcd']) && $_POST['download_vcd']=='on') array_push($file_types,".vcd");
				if(isset($_POST['download_log']) && $_POST['download_log']=='on') array_push($file_types,".log");
				if( $user->validate_edit_rights($editors) && !empty($file_types) ){
					$path = $BASE_DIR.$user->username."/".$project['short_code']."/";
					$files = $gen->get_directory_files($path,$file_types,$project['short_code']);
					
					$zip = new ZipArchive();
					$zip_name = $files[0].".zip";
					if( $zip->open($path.$zip_name,  ZipArchive::CREATE) === true){
						$gen->addfiles_to_zip($zip,$files,$path,'');
						$zip->close();
						if(file_exists($path.$zip_name) && is_readable($path.$zip_name)){
							header('Content-Type: application/zip');
							header('Content-disposition: attachment; filename='.$zip_name);
							header('Content-Length: ' . filesize($path.$zip_name));
							header("Pragma: no-cache"); 
							header("Expires: 0"); 
							ob_clean();
							flush();
							readfile($path.$zip_name);
							unlink($path.$zip_name);
							exit();
						}
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			// Create File
			case "Create_file":
				$name = $gen->create_short_code($_POST['file_name']);
				$owner = $db->get_user_information($_POST['owner'],"username");
				$path = $BASE_DIR.$owner['username']."/".$_POST['project_shortcode']."/".$name;
				$project = $db->get_project_shortcode($_POST['project_shortcode'], $owner['id']);
				$editors = $db->get_project_editors($project['id']);
				$file_exists = $db->check_file_exist($name, $project['id']);
				if( $user->validate_edit_rights($editors) ){
					if(!$file_exists){
						if( fopen($path,"w+") ){				
							if($db->add_file($name, $project['id']) > 0){
								array_push($_SESSION['vhdl_msg'], 'success_create_file');
								header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']);
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
				$owner = $db->get_user_information($_POST['owner'],"username");
				$uploadfile = $path . $name;
				$project = $db->get_project_shortcode($_POST['project_shortcode'], $owner['id']);
				$editors = $db->get_project_editors($project['id']);
				$file_exists = $db->check_file_exist($name, $project['id']);
				$owner_used_space = ( filesize($BASE_DIR.$owner['username']) + $_FILES['userfile']['size'] ) / pow(1024,2);
				
				if($owner['available_space'] < $owner_used_space || $_FILES['userfile']['size']>5242880){
					array_push($_SESSION['vhdl_msg'], 'space_fail');
					header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']);
					exit();
				}
				if( $user->validate_edit_rights($editors) ){
					if($file_exists){
						array_push($_SESSION['vhdl_msg'], 'file_exists');
						header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']);
						exit();			
					}

					//Check for errors
					if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) { 
					//uploading successfully done 
					} else { 
						array_push($_SESSION['vhdl_msg'], 'fail_upload_file');
						header("Location:".$BASE_URL."/project/".$_POST['owner']."/".$_POST['project_shortcode']);
						exit();
					} 
					
					$file_id = $db->add_file($name, $project['id']);
					if($file_id > 0){		
						
						$ret=move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
						if ($ret) {
							
							$checkfile = pathinfo($uploadfile);
							if ( $checkfile['extension'] == "zip" || $checkfile['extension'] == "ZIP" ){

								if( $gen->extract_file($uploadfile,$path, $project['id']) ){
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
				$owner = $db->get_project_owner($project['id']);
				if( $user->validate_edit_rights($editors) ){
					foreach ($selected as $file_id) {
						$file = $db->get_file($file_id);
						$full_path = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/'.$file['name'];
						$directory = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/';

						if (file_exists($full_path)){
							$extra=$gen->process_pre_options($_POST);
							//$prefile="-a ".$extra;
							$prefile="-a ";
							$postfile="";
							$executable='/usr/lib/ghdl/bin/ghdl';
							$timeout=6;
							if( file_exists($full_path.".log") ){
								unlink($full_path.".log");
							}
							$gen->create_job_file($JOBDIRECTORY,$directory,$file['name'],$prefile,$postfile,$executable,$timeout);
							$db->file_compile_pending($file['id']);
							
							array_push($_SESSION['vhdl_msg'], 'compile_success');
						}else{
							array_push($_SESSION['vhdl_msg'], 'compile_fail');
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
					$architectureclean=$_POST['architecture'];	
					$explodedarchitecture=explode(" ",$architectureclean);
					$unit=$explodedarchitecture[0];
					$architecture=$explodedarchitecture[1];	
					
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
							$extra_post = " --vcd=".$project['short_code'].".vcd ";
						}
					}
					$postfile=$architecture.$extra_post;
					
					$gen->create_job_file($JOBDIRECTORY,$directory,$unit,$prefile,$postfile);
					array_push($_SESSION['vhdl_msg'], 'simulation_success');
					
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			case "Remove_Selected":
				$selected = explode('-',$_POST['selected_ids']);
				$project = $db->get_project($_POST['project_id']);
				$owner = $db->get_project_owner($project['id']);
				$editors = $db->get_project_editors($project['id']);
				if( $user->validate_edit_rights($editors) ){
					foreach ($selected as $file_id) {
						$file = $db->get_file($file_id);
						if(!empty($file)){
							$full_path = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/'.$file['name'];

							$result = true;

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
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;

			case "Post_Library_Selected":
				$selected = explode('-',$_POST['selected_ids']);
				$project = $db->get_project($_POST['project_id']);
				$editors = $db->get_project_editors($project['id']);
				$owner = $db->get_project_owner($project['id']);
				if( $user->validate_edit_rights($editors) ){
					foreach ($selected as $file_id) {
						$file = $db->get_file($file_id);
						$full_path = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/'.$file['name'];

						if( !is_dir($full_path) ){
							
							if($db->is_new_library($file_id)){							
								$counter=1;
								$library_name = $file['name'];
								if($db->check_lib_exist($library_name)){
									while($db->check_lib_exist($library_name)){
										$library_name = "[".$counter."]".$library_name;
										$counter++;
									}
								}
								
								$result=true;
								$lib_id = $db->add_library($file['name'],$owner['username'],$file_id);
								if( !($lib_id > 0) ){
									$result=false;
								}		

								if(file_exists($full_path)){
									if( !copy($full_path,$BASE_DIR."libraries/".$library_name) ){
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
								$result=true;
								$library=$db->get_library_file_id($file_id);
								if(!file_exists($BASE_DIR."update_libraries/".$library['name'])){
									$update_id = $db->suggest_update_library($library['id'],$library['name']);
									if( !($update_id > 0) ){
										$result=false;
									}	
								}else{
									unlink($BASE_DIR."update_libraries/".$library['name']);
								}								

								if (file_exists($full_path)){
									if( !copy($full_path,$BASE_DIR."update_libraries/".$library['name']) ){
										$result=false;
									}
								}else{
									$result=false;
								}
								
								if($result){
									array_push($_SESSION['vhdl_msg'], 'suggest_library_success');
								}else{
									array_push($_SESSION['vhdl_msg'], 'suggest_library_fail');
								}
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
				$library = $db->get_library_id($_POST['library_id']);
				$owner = $db->get_project_owner($project['id']);

				$full_path = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/'.$library['name'];
				$libs_path = $BASE_DIR.'libraries/'.$library['name'];
				$directory = $BASE_DIR.$owner['username'].'/'.$project['short_code'].'/';

				$result=true;
				$editors = $db->get_project_editors($project['id']);
				if( $user->validate_edit_rights($editors) ){
					if(!is_dir($directory)){
						$result=false;
					}
					
					if( copy($libs_path, $full_path) ){
						chmod($full_path, fileperms($libs_path));
					}else{
						$result=false;
					}
					
					if($db->check_file_exist($library['name'], $project['id'])){
						if(!($db->update_file_component($library, $project['id']) > 0)){
							$result=false;
						}
					}else{
						if(!($db->add_file_component($library, $project['id']) > 0)){
							$result=false;
						}
					}
					
					if($result){
						array_push($_SESSION['vhdl_msg'], 'import_library_success');
						header("Location:".$BASE_URL."/project/".$owner['username']."/".$project['short_code']);
						exit();
					}else{
						array_push($_SESSION['vhdl_msg'], 'import_library_fail');
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
			break;
			
			case "Apply_Update_Admin":
				if( $user->type=='1' ){
					
					$result=true;
					$suggestion = $db->get_library_suggestion($_POST['library_id']);
					$suggestion_path = $BASE_DIR."update_libraries/".$suggestion['library'];
					$library_path = $BASE_DIR."libraries/".$suggestion['library'];							

					if (file_exists($suggestion_path)){
						if( copy($suggestion_path,$library_path) ){
							unlink($suggestion_path);
							$db->remove_library_suggestion($_POST['library_id']);
							$db->increase_library_version($_POST['library_id']);
						}else{
							$result=false;
						}
					}else{
						$result=false;
					}
					
					if($result){
						array_push($_SESSION['vhdl_msg'], 'suggest_approve_success');
					}else{
						array_push($_SESSION['vhdl_msg'], 'suggest_approve_fail');
					}
					
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
				header("Location:".$BASE_URL."/admin/components/");
				exit();
			break;
			
			case "Discard_Update_Admin":
				if( $user->type=='1' ){
					
					$suggestion = $db->get_library_suggestion($_POST['library_id']);
					$full_path = $BASE_DIR."update_libraries/".$suggestion['library'];
					
					if(file_exists($full_path)){
						unlink($full_path);
					}
					
					if( $db->remove_library_suggestion($_POST['library_id']) ){
						array_push($_SESSION['vhdl_msg'], 'suggest_remove_success');
					}else{
						array_push($_SESSION['vhdl_msg'], 'suggest_remove_fail');
					}
					
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
				}
				header("Location:".$BASE_URL."/admin/components/");
				exit();
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
				$owner_used_space = ( filesize($BASE_SID.$_SESSION['SID']) + $_FILES['userfile']['size'] ) / pow(1024,2);
				
				if(20 < $owner_used_space || $_FILES['userfile']['size']>5242880){
					array_push($_SESSION['vhdl_msg'], 'space_fail');
					header("Location:".$BASE_URL);
					exit();
				}
				
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

							if( $gen->extract_file($uploadfile,$path, $project['id']) ){
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
						$extra=$gen->process_pre_options($_POST);
						//$prefile="-a ".$extra;
						$prefile="-a ";
						$postfile="";
						$executable='/usr/lib/ghdl/bin/ghdl';
						$timeout=6;
						if( file_exists($full_path.".log") ){
							unlink($full_path.".log");
						}
						$gen->create_job_file($JOBDIRECTORY,$directory,$file['name'],$prefile,$postfile,$executable,$timeout);
						$db->file_compile_pending_sid($file['id']);

						array_push($_SESSION['vhdl_msg'], 'compile_success');
					}else{
						array_push($_SESSION['vhdl_msg'], 'compile_fail');
					}
				}
			break;
				
			case "SID_Simulate_Project":
				$directory = $BASE.$_SESSION['SID'].'/';
				$architectureclean=$_POST['architecture'];	
				$explodedarchitecture=explode(" ",$architectureclean);
				$unit=$explodedarchitecture[0];
				$architecture=$explodedarchitecture[1];

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

				$gen->create_job_file($JOBDIRECTORY,$directory,$unit,$prefile,$postfile);
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
			
			// Edit user
			case "Edit_User":
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
					if($db->edit_user($new_pass,$phone,$_POST["email_edit"],$_POST["ace_theme"],$user->id, NULL)){
						array_push($_SESSION['vhdl_msg'], 'success_edit_user');
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_edit_user');
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'fail_edit_user');	
				}
			break;
			
			// Edit user By Admin
			case "Edit_User_Admin":
				if( $user->type==1 ){
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
					if($db->edit_user($new_pass,$phone,$_POST["email_edit"],$_POST["ace_theme"],$_POST['user_id_edit'],$_POST['available_space_edit'])){
						array_push($_SESSION['vhdl_msg'], 'success_edit_user');
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_edit_user');
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'fail_edit_user');	
				}
			break;
			
			// Remove User by Admin
			case "Remove_User_Admin":
				if( $user->type==1 && isset($_POST['user_id']) && $_POST['user_id']>0){
					$reming_user = $db->get_user_information($_POST['user_id'], "id");
					$db->remove_user_editor($reming_user['id']);
					$user_projects = $db->get_user_projects($reming_user['id']);
					$user_path = $BASE_DIR.$reming_user['username'];
					$result = true;
					
					foreach($user_projects as $project){ 
						$full_project_path = $user_path.'/'.$project['short_code'];
						$db->clear_project($project['id']);
						$db->remove_project($project['id']);
						if (file_exists($full_project_path)){
							system("rm -rf ".escapeshellarg($full_project_path));
						}else{
							$result=false;
						}
					}
					
					if (file_exists($user_path)){
						system("rm -rf ".escapeshellarg($user_path));
					}else{
						$result=false;
					}
					
					if( ! $db->remove_user($reming_user['id']) ){
						$result=false;	
					}
					
					if($result){
						array_push($_SESSION['vhdl_msg'], 'success_remove_user');	
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_remove_user');	
					}
					
					header("Location:".$BASE_URL."/admin");
					exit();
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
					header("Location:".$BASE_URL);
					exit();
				}
			break;
			
			// Create user by Admin
			case "Create_User_Admin":
				if( $user->type==1){
					if($_POST["password"] == $_POST["password_confirm"]){
						if(strlen($_POST["telephone"])!=10){
							$phone=NULL;
						}else{
							$phone=$_POST["telephone"];
						}
						$code = $gen->generate_code();
						if($db->register_user($_POST["username"],$_POST["password"],$_POST["email"],$phone,$code,$_POST["active"],$_POST["type"])){
							if($_POST["active"]==0){
								if($phone!=NULL){
									$message="Your activation code is : ".$code;
									$gen->send_sms($message,$phone);
								}
								$subject = "HDL Everywhere Registration";
								$message = "Welcome to our website!\r\rYou, or someone using your email address, has completed registration at HDL Everywhere. You can complete registration by clicking the following link:\r http://snf-703457.vm.okeanos.grnet.gr/vhdl/ \rAnd after logging in, enter the Activation Code : ".$code." \r\r If this is an error, ignore this email and you will be removed from our mailing list.";
								$gen->send_email($message,$subject,$_POST["email"]);
							}
							array_push($_SESSION['vhdl_msg'], 'success_register');	
							mkdir($BASE_DIR.$_POST["username"],0777);
						}else{
							array_push($_SESSION['vhdl_msg'], 'fail_register');
						}
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_register_confirm');	
					}
					header("Location:".$BASE_URL."/admin");
					exit();
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
					header("Location:".$BASE_URL);
					exit();
				}
			break;
			
			// Remove Project by Admin
			case "Remove_Project_Admin":
				if( $user->type==1){
					$project = $db->get_project($_POST['project_id']);
					$owner = $db->get_project_owner($_POST['project_id']);
					$full_path = $BASE_DIR.$owner['username'].'/'.$project['short_code'];
					$db->clear_project($project['id']);
					$db->remove_project($project['id']);
					if (file_exists($full_path)){
						system("rm -rf ".escapeshellarg($full_path));
					}
					array_push($_SESSION['vhdl_msg'], 'remove_project_success');
					header("Location:".$BASE_URL."/admin/projects");
					exit();
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
					header("Location:".$BASE_URL);
					exit();
				}
			break;
			
			// Approve Component by Admin
			case "Approve_Component_Admin":
				if( $user->type==1){
					if( $db->approve_library_admin($_POST['library_id']) ){
						array_push($_SESSION['vhdl_msg'], 'approve_component_success');
					}else{
						array_push($_SESSION['vhdl_msg'], 'approve_component_fail');
					}
					header("Location:".$BASE_URL."/admin/components");
					exit();
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
					header("Location:".$BASE_URL);
					exit();
				}
			break;
			
			// Disapprove Component by Admin
			case "Disapprove_Component_Admin":
				if( $user->type==1){
					if( $db->disapprove_library_admin($_POST['library_id']) ){
						array_push($_SESSION['vhdl_msg'], 'disapprove_component_success');
					}else{
						array_push($_SESSION['vhdl_msg'], 'disapprove_component_fail');
					}
					header("Location:".$BASE_URL."/admin/components");
					exit();
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
					header("Location:".$BASE_URL);
					exit();
				}
			break;
			
			// Remove Component by Admin
			case "Remove_Component_Admin":
				if( $user->type==1){
					$library = $db->get_library_id($_POST['library_id']);
					
					if(!empty($library)){
						$full_path = $BASE_DIR.'libraries/'.$library['name'];
						$result = true;

						if( !$db->remove_library($library['id']) ){
							$result=false;
						}
						if (file_exists($full_path)){
							system("rm -rf ".escapeshellarg($full_path));
						}else{
							$result=false;
						}

						if($result){
							array_push($_SESSION['vhdl_msg'], 'library_removed_success');
						}else{
							array_push($_SESSION['vhdl_msg'], 'library_removed_fail');
						}
					}
					header("Location:".$BASE_URL."/admin/components");
					exit();
					
				}else{
					array_push($_SESSION['vhdl_msg'], 'permissions_fail');
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
				$_SESSION['vhdl_user']['type'] = $db->user_type($id);
				$user = new User($_SESSION['vhdl_user']);
				array_push($_SESSION['vhdl_msg'],"success_login");
			}else{
				array_push($_SESSION['vhdl_msg'],"fail_login");
			}
			header("Location:".$BASE_URL);
			exit();
		break;

		// register user
		case "register":
				if($_POST["password"] == $_POST["password_confirm"]){
					if(strlen($_POST["telephone"])!=10){
						$phone=NULL;
					}else{
						$phone=$_POST["telephone"];
					}
					$code = $gen->generate_code();
					if($db->register_user($_POST["username"],$_POST["password"],$_POST["email"],$phone,$code,0,0)){
						if($phone!=NULL){
							$message="Your activation code is : ".$code;
							$gen->send_sms($message,$phone);
						}
						$subject = "HDL Everywhere Registration";
						$message = "Welcome to our website!\r\rYou, or someone using your email address, has completed registration at HDL Everywhere. You can complete registration by clicking the following link:\r http://snf-703457.vm.okeanos.grnet.gr/vhdl/ \rAnd after logging in, enter the Activation Code : ".$code." \r\r If this is an error, ignore this email and you will be removed from our mailing list.";
						$gen->send_email($message,$subject,$_POST["email"]);
						array_push($_SESSION['vhdl_msg'], 'success_register');	
						mkdir($BASE_DIR.$_POST["username"],0777);
					}else{
						array_push($_SESSION['vhdl_msg'], 'fail_register');
					}
				}else{
					array_push($_SESSION['vhdl_msg'], 'fail_register_confirm');	
				}
			header("Location:".$BASE_URL);
			exit();
		break;
		
		case "set_sid":
			$_SESSION['SID']=intval($_POST['pid']);
			$_SESSION['vhdl_user']['username'] = "Guest";
			$_SESSION['vhdl_user']['id'] = "0";
			$_SESSION['vhdl_user']['loged_in'] = 1;
			$_SESSION['vhdl_user']['activated'] = 1;
			$_SESSION['vhdl_user']['type'] = 0;
			$user = new User($_SESSION['vhdl_user']);
		break;
			
		case "new_sid":
			srand();
			$number=rand()%100000;
			while(!mkdir($BASE.$number,0777)){
				$number=rand()%100000;
			}
			// Ok we found a new random file
			$_SESSION['SID']=$number;
			$_SESSION['vhdl_user']['username'] = "Guest";
			$_SESSION['vhdl_user']['id'] = "0";
			$_SESSION['vhdl_user']['loged_in'] = 1;
			$_SESSION['vhdl_user']['activated'] = 1;
			$_SESSION['vhdl_user']['type'] = 0;
			$user = new User($_SESSION['vhdl_user']);
		break;
			
	}
}
?>