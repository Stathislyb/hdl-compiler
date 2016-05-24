<?php
class User {
	// User class variables
	public $username,$id,$logged_in,$type;
	
	// Class constractor function
	//  initialise the user variables through the session
	public function __construct($vhdl_user){
		$this->username = $vhdl_user['username'];
		$this->id = $vhdl_user['id'];
		$this->logged_in = $vhdl_user['loged_in'];
		$this->type = $vhdl_user['type'];
	}
	public function __destruct(){
		$this->username = null;
		$this->id = null;
		$this->logged_in = null;
		$this->type = null;
	}
	
	// Return true if the the user is the owner of the project
	public function validate_ownership($editors){
		if($this->type=='1'){
			$valid = true; 
		}else{
			$valid = false; 
			foreach($editors as $editor){
				if( $editor['username'] == $this->username && $editor['user_type'] == 1){
					$valid = true;
				}
			}
		}
		return $valid;
	}
	
	// Return true if the the user is an editor to the project
	public function validate_edit_rights($editors){
		if($this->type=='1'){
			$valid = true; 
		}else{
			$valid = false; 
			foreach($editors as $editor){
				if( $editor['username'] == $this->username){
					$valid = true;
				}
			}
		}
		return $valid;
	}
	
}
?>