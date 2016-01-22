<?php

class Messages {
	// Database class variables
	public $messages=array(
		"fail_login" => "The Username or Password were wrong.",
		"success_login" => "Logged in successfully.",
		"success_register" => "Registered user successfully. An e-mail will be sent to the adress you provided with details about activating your account.",
		"fail_register" => "The user could not be registered. Make sure the values you gave were correct and try a different username.",
		"fail_register_confirm" => "The confirmation password did not match.",
		"invalid_mail" => "The e-mail address you provided was invalid.",
		"success_project_creation" => "The project was created successfully.",
		"fail_project_creation" => "The project could not be created.",
		"success_project_edit" => "The project was edited successfully.",
		"fail_project_edit" => "The project changes could not be saved.",
		"success_create_dir" => "The directory was created successfully.",
		"fail_create_dir" => "The directory could not be created.",
		"success_create_file" => "The file was created successfully.",
		"fail_create_file" => "The file could not be created."
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
			echo $this->messages[$msg_id]."<br />";
		}
		$_SESSION['vhdl_msg']=array();
	}
}




?>