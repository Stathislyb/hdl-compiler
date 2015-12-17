<?php
class User {
	// User class variables
	public $username,$id,$logged_in;
	
	// Class constractor function
	//  initialise the user variables through the session
	public function __construct($vhdl_user){
		$this->username = $vhdl_user['username'];
		$this->id = $vhdl_user['id'];
		$this->logged_in = $vhdl_user['loged_in'];
	}
	public function __destruct(){
		$this->username = null;
		$this->id = null;
		$this->logged_in = null;
	}
	
	public function validate_ownership($editors){
		$valid = false; 
		foreach($editors as $editor){
			if( $editor['username'] == $this->username && $editor['user_type'] == 1){
				$valid = true;
			}
		}
		return $valid;
	}
	
}
?>