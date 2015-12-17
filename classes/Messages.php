<?php

class Messages {
	// Database class variables
	public $messages=array(
	"fail_login" => "The Username or Password were wrong.",
	"success_login" => "Logged in successfully.",
	"success_register" => "Registered user successfully. An e-mail will be sent to the adress you provided with details about activating your account.",
	"fail_register" => "The user could not be registered. Make sure the values you gave were correct and try a different username.",
	"fail_register_confirm" => "The confirmation password did not match.",
<<<<<<< HEAD
	"invalid_mail" => "The e-mail address you provided was invalid.",
	"success_project_creation" => "The project was created successfully.",
	"fail_project_creation" => "The project could not be created."
=======
<<<<<<< HEAD
	"invalid_mail" => "The e-mail address you provided was invalid."
=======
>>>>>>> 00fa1210890c7e9040427907264ca63341466945
>>>>>>> ac9df3641598402b41bfd9320c1517fda865170b
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