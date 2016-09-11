<?php

class Messages {
	// Messages array
	//success, info, warning, danger
	public $messages=array(
		"fail_login" => array("The Username or Password were wrong.","danger"),
		"success_login" => array("Logged in successfully.","success"),
		"success_register" => array("Registered user successfully. An e-mail will be sent to the adress you provided with details about activating your account.","success"),
		"fail_register" => array("The user could not be registered. Make sure the values you gave were correct and try a different username.","danger"),
		"success_edit_user" => array("Edit user successfully.","success"),
		"fail_edit_user" => array("The user could not be edited. Make sure the values you gave were correct.","danger"),
		"fail_register_confirm" => array("The confirmation password did not match.","danger"),
		"invalid_mail" => array("The e-mail address you provided was invalid.","danger"),
		"success_project_creation" => array("The project was created successfully.","success"),
		"fail_project_creation" => array("The project could not be created.","danger"),
		"invalid_project_name" => array("The project name was not valid.","danger"),
		"success_project_edit" => array("The project was edited successfully.","success"),
		"fail_project_edit" => array("The project changes could not be saved.","danger"),
		"success_create_dir" => array("The directory was created successfully.","success"),
		"fail_create_dir" => array("The directory could not be created.","danger"),
		"success_create_file" => array("The file was created successfully.","success"),
		"fail_create_file" => array("The file could not be created.","danger"),
		"success_upload_file" => array("The was successfully uploaded.","success"),
		"fail_upload_file" => array("The could not be uploaded.","danger"),
		"fail_upload_file_unzip" => array("Error occured while unzipping the file.","danger"),
		"file_exists"=>array("A file with the same name already exists in this directory.","danger"),
		"file_removed_success"=>array("The file was successfully removed.","success"),
		"file_removed_fail"=>array("Failed to remove the file.","danger"),
		"add_library_success"=>array("The library was successfully published. An administrator will have to approve it.","success"),
		"add_library_fail"=>array("Failed to publish the library.","danger"),
		"suggest_library_success"=>array("The suggestion for updating the library was successfully added.","success"),
		"suggest_library_fail"=>array("Failed to suggest the update for the library.","danger"),
		"suggest_approve_success"=>array("The suggestion was successfully approved.","success"),
		"suggest_approve_fail"=>array("Failed to approve the suggested update for the library.","danger"),
		"suggest_remove_success"=>array("The suggestion was successfully discarded.","success"),
		"suggest_remove_fail"=>array("Failed to discard the suggested update for the library.","danger"),
		"import_library_success"=>array("The library was imported in your project successfully.","success"),
		"import_library_fail"=>array("Failed to import the library in your project.","danger"),
		"permissions_fail"=>array("You do not have the rights to perform the requested action.","danger"),
		"remove_project_success"=>array("The project was successfully removed.","success"),
		"compile_fail"=>array("Failed to compile the file.","danger"),
		"compile_success"=>array("The file is successfully queued for compiling.","success"),
		"simulation_success"=>array("The file is successfully queued for simulation.","success"),
		"activation_success"=>array("Your account has successfully been activated.","success"),
		"activation_fail"=>array("The activation code you provided was not correct.","danger"),
		"space_fail"=>array("You have reached the limits of your available space.","danger"),
		"success_remove_user" => array("The user was removed successfully.","success"),
		"fail_remove_user" => array("The user was not properly removed.","danger"),
		"approve_component_success" => array("The component was approved successfully.","success"),
		"approve_component_fail" => array("Failed to approve the component.","danger"),
		"disapprove_component_success" => array("The component was disapproved successfully.","success"),
		"disapprove_component_fail" => array("Failed to disapprove the component.","danger"),
		"library_removed_success" => array("The component was removed successfully.","success"),
		"library_removed_fail" => array("Failed to remove the component.","danger"),
		"invalid_POST" => array("Some of the posted data did not have values of the correct format.","danger")
	);
	
	// Text array
	/*
	public $text=array(
		"en" => array(
			"home_header" => "HDL Everywhere.",
			[...]
		),
		"gr" => array(
			"home_header" => "HDL Παντού.",
			[...]
		)	
	);
	*/
	
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