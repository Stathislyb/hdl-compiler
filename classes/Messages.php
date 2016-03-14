<?php

class Messages {
	// Database class variables
	//success, info, warning, danger
	public $messages=array(
		"fail_login" => array("The Username or Password were wrong.","danger"),
		"success_login" => array("Logged in successfully.","success"),
		"success_register" => array("Registered user successfully. An e-mail will be sent to the adress you provided with details about activating your account.","success"),
		"fail_register" => array("The user could not be registered. Make sure the values you gave were correct and try a different username.","danger"),
		"fail_register_confirm" => array("The confirmation password did not match.","danger"),
		"invalid_mail" => array("The e-mail address you provided was invalid.","danger"),
		"success_project_creation" => array("The project was created successfully.","success"),
		"fail_project_creation" => array("The project could not be created.","danger"),
		"success_project_edit" => array("The project was edited successfully.","success"),
		"fail_project_edit" => array("The project changes could not be saved.","danger"),
		"success_create_dir" => array("The directory was created successfully.","success"),
		"fail_create_dir" => array("The directory could not be created.","danger"),
		"success_create_file" => array("The file was created successfully.","success"),
		"fail_create_file" => array("The file could not be created.","danger"),
		"success_upload_file" => array("The was successfully uploaded.","success"),
		"fail_upload_file" => array("The could not be uploaded.","danger"),
		"fail_upload_file_unzip" => array("Error occured while unzipping the file.","danger"),
		"file_exists"=>array("A file with the same name already exists in this directory","danger"),
		"file_removed_success"=>array("The file was successfully removed","success"),
		"file_removed_fail"=>array("Failed to remove the file","danger"),
		"add_library_success"=>array("The library was successfully published","success"),
		"add_library_fail"=>array("Failed to publish the library","danger"),
		"import_library_success"=>array("The library was imported in your project successfully","success"),
		"import_library_fail"=>array("Failed to import the library in your project","danger")
	);
	
	// Class constractor function
	public function __construct(){
	}
	// Class destractor function
	public function __destruct(){
		$this->messages = null;
	}
	
	// Display messages
	public function display_msg($msg_codes_array) {
		foreach($msg_codes_array as $msg_id){
			
			echo '<div class="alert alert-'.$this->messages[$msg_id][1].'">'
   					.'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>'
    				.$this->messages[$msg_id][0]
				.'</div>';
		}
		$_SESSION['vhdl_msg']=array();
	}
}




?>