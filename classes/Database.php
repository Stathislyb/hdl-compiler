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
	
	// Register user in database, returns id on success and 0 on failure
	public function register_user($username, $password, $email) {
		$password = md5($password);
		$query = "INSERT INTO users (username, password, email) Values('".$username."','".$password."','".$email."')"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
	
	// Select the user's projects, returns a list of the projects on success and false on failure
	public function get_user_projects($user_id) {
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = '".$user_id."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the user's information by search parameter
	public function get_user_information($user, $search_type) {
		$query = "SELECT * FROM users WHERE  ".$search_type."= '".$user."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Select project by id, returns the project on success and false on failure
	public function get_project($project_id) {
		$query = "SELECT * FROM projects WHERE id='".$project_id."' "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Select project by short-code, returns the project on success and false on failure
	public function get_project_shortcode($project_code, $user_id) {
		$query = "SELECT projects.* FROM projects JOIN projects_editors ON projects_editors.project_id = projects.id WHERE projects_editors.user_id='".$user_id."' AND projects.short_code = '".$project_code."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Select project editors by project id, returns the project's editors on success and false on failure
	public function get_project_editors($project_id) {
		$query = "SELECT users.username,projects_editors.user_type FROM users JOIN projects_editors ON projects_editors.user_id = users.id WHERE projects_editors.project_id = '".$project_id."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
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
	
	// Add multiple editors to the project, returns true on success and flase on failure
	public function add_project_multi_editors($users, $project_id) {
		$users_array = explode(",", $users);
		
		$query_remove_old = "DELETE FROM projects_editors WHERE user_type = '0' AND project_id = '".$project_id."' "; 
		$statement = $this->conn->prepare($query_remove_old); 
		$statement->execute();
		
		$query_verify = "SELECT user_id FROM projects_editors WHERE user_type = '1' AND project_id = '".$project_id."' "; 
		$statement = $this->conn->prepare($query_verify); 
		$statement->execute();
		$result = $statement->fetch();
		
		foreach($users_array as $user){
			$query = "SELECT id FROM users WHERE username = '".$user."'"; 
			$statement = $this->conn->prepare($query); 
			$statement->execute();
			$user_res = $statement->fetch();
			
			if($result['user_id'] != $user_res['id']){
				$query_insert = "INSERT INTO projects_editors (user_id, project_id, user_type) Values('".$user_res['id']."','".$project_id."','0')"; 
				$statement = $this->conn->prepare($query_insert); 
				$statement->execute();
			}
		}
				
		return;
	}
	
	// Create new project, returns true on success and false on failure
	public function edit_project($name, $description, $short_code, $project_id) {
		$query = "UPDATE projects SET name='".$name."', description='".$description."', short_code='".$short_code."' WHERE projects.id='".$project_id."'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
	
	// Select the user's projects, returns a list of the projects on success and false on failure
	public function get_project_files($user_id, $project_shortcode, $dir) {
		$query = "SELECT project_files.* FROM projects JOIN projects_editors ON projects_editors.project_id = projects.id JOIN project_files ON project_files.project_id = projects.id WHERE projects_editors.user_id = '".$user_id."' AND projects_editors.user_type = '1' AND projects.short_code = '".$project_shortcode."' AND relative_path='".$dir."' ORDER BY project_files.type"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Add directory in database
	public function add_directory($dir_name, $project_id, $current_dir) {
		$query = "INSERT INTO project_files (name, project_id, type, relative_path) Values('".$dir_name."','".$project_id."' ,'directory' ,'".$current_dir."')"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
}




?>