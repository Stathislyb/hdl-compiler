<?php

class Database {
	// Database class variables
	public $conn;
	
	// Class constractor function
	//  initialise the PDO connection
	public function __construct(){
		$db_host="localhost"; // Host name 
		$db_username="root"; // Mysql username 
		$db_password=""; // Mysql password 
		$db_name="vhdl_compiler"; // Database name 

		try{
			$this->conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8",$db_username,$db_password);
		}
			catch(PDOException $pe){
			die('Connection error:' . $pe->getmessage());
		}
	}
	public function __destruct(){
		$this->conn = null;
	}
	
	// Confirm username and password
	//  on success returns user ID, else 0
	public function confirm_user($username, $password) {
		$password = md5($password);
		$query = "SELECT * FROM users WHERE username = '".$username."'"; 
		$statement = $this->conn->prepare($query); 
		
		if($statement->execute()){
			$result = $statement->fetch();
			if($result['password'] == $password){
				return $result['id'];
			}
		}
		
		return 0;
	}
	
	// Register user in database
	public function register_user($username, $password) {
		$password = md5($password);
		$query = "INSERT INTO users (username, password) Values('".$username."','".$password."')"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
}




?>