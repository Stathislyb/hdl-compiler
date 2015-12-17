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
	
<<<<<<< HEAD
	// Register user in database, returns id on success and 0 on failure
=======
<<<<<<< HEAD
	// Register user in database, returns id on success and false on failure
>>>>>>> ac9df3641598402b41bfd9320c1517fda865170b
	public function register_user($username, $password, $email) {
		$password = md5($password);
		$query = "INSERT INTO users (username, password, email) Values('".$username."','".$password."','".$email."')"; 
		$statement = $this->conn->prepare($query); 
<<<<<<< HEAD
		$statement->execute();
		return $this->conn->lastInsertId();
=======
		return $statement->execute();
>>>>>>> ac9df3641598402b41bfd9320c1517fda865170b
	}
	
	// Select the user's projects, returns a list of the projects on success and false on failure
	public function get_user_projects($user_id) {
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = '".$user_id."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
<<<<<<< HEAD
	
	// Create new project, returns id on success and 0 on failure
	public function create_project($name, $description, $short_code) {
		$query = "INSERT INTO projects (name, description, short_code) Values('".$name."','".$description."','".$short_code."')"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
	
	// Add editor/owner to project, returns id on success and 0 on failure
	public function add_project_editor($username, $project_id, $user_type) {
		$query = "SELECT id FROM users WHERE username = '".$username."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$user = $statement->fetch();
		$query = "INSERT INTO projects_editors (user_id, project_id, user_type) Values('".$user['id']."','".$project_id."','".$user_type."')"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
=======
=======
	// Register user in database
	public function register_user($username, $password) {
		$password = md5($password);
		$query = "INSERT INTO users (username, password) Values('".$username."','".$password."')"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
>>>>>>> 00fa1210890c7e9040427907264ca63341466945
>>>>>>> ac9df3641598402b41bfd9320c1517fda865170b
}




?>