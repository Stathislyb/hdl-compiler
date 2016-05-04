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
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = '".$user_id."' AND user_type='1'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the user's public projects, returns a list of the projects on success and false on failure
	public function get_user_projects_public($user_id) {
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = '".$user_id."' AND user_type='1' AND public='1'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the projects in which the user is an editor but not the owner, returns a list of the projects on success and false on failure
	public function get_shared_projects($user_id) {
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = '".$user_id."' AND user_type='0'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$projects = $statement->fetchAll();
		foreach ($projects as $key=>$project) {
			$query = "SELECT users.username FROM projects_editors INNER JOIN users ON projects_editors.user_id = users.id WHERE projects_editors.project_id = '".$project['id']."' AND user_type='1'"; 
			$statement = $this->conn->prepare($query); 
			$statement->execute();
			$owner = $statement->fetch();
			$projects[$key]['owner'] = $owner['username'] ;
		}
		return $projects;
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
	
	// Select project by name
	public function get_project_name($project_name) {
		$query = "SELECT * FROM projects WHERE name='".$project_name."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Select project's owner and return his username
	public function get_project_owner($project_id) {
		$query = "SELECT users.username FROM projects JOIN projects_editors ON projects_editors.project_id = projects.id JOIN users ON users.id = projects_editors.user_id WHERE projects.id='".$project_id."' AND projects_editors.user_type = '1'"; 
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
	public function create_project($name, $description, $short_code, $public) {
		$query = "INSERT INTO projects (name, description, short_code, public) Values('".$name."','".$description."','".$short_code."','".$public."')"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
	
	// Create new project, returns id on success and 0 on failure
	public function remove_project($project_id) {
		$query = "DELETE FROM projects WHERE id='".$project_id."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$query = "DELETE FROM projects_editors WHERE project_id='".$project_id."'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
	
	// Create new project, returns id on success and 0 on failure
	public function clear_project($project_id) {
		$query = "DELETE FROM project_files WHERE project_id='".$project_id."'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
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
	public function edit_project($name, $description, $short_code, $project_id, $share_project) {
		$query = "UPDATE projects SET name='".$name."', description='".$description."', short_code='".$short_code."', public='".$share_project."' WHERE projects.id='".$project_id."'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
	
	// Select the project's files, returns a list of the project's files
	public function get_project_files($project_id, $dir) {
		$query = "SELECT * FROM project_files WHERE project_id = '".$project_id."' AND relative_path='".$dir."' ORDER BY type"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the file by id, returns the file's row on success and false on failure
	public function get_file($file_id) {
		$query = "SELECT * FROM project_files WHERE id = '".$file_id."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Select the file by id, returns the file's row on success and false on failure
	public function get_file_byname($file,$project) {
		$query = "SELECT * FROM project_files WHERE project_id = '".$project."' AND name='".$file."' "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Add directory or file in database
	public function add_dir_file($dir_name, $project_id, $file_type,  $current_dir) {
		$query = "INSERT INTO project_files (name, project_id, type, relative_path) Values('".$dir_name."','".$project_id."' ,'".$file_type."' ,'".$current_dir."')"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
	
	// Check if a file/dir exists already, return true or false
	public function check_file_dir_exist($dir_name, $project_id,  $current_dir) {
		$query = "SELECT COUNT(*) FROM project_files WHERE project_id = '".$project_id."' AND name='".$dir_name."' AND relative_path='".$current_dir."' "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$result = $statement->fetch();
		if($result['COUNT(*)'] > 0){
			return true;
		}else{
			return false;
		}
	}
	
	// Removes file from database, return true or false
	public function remove_file($file_id) {
		$query = "DELETE FROM project_files WHERE id = '".$file_id."'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
	
	// Removes files in the given directory from database, return true or false
	public function remove_inner_files($file_id) {
		$file = $this->get_file($file_id);
		if($file['relative_path']=='/'){
			$relative_dir = '/'.$file['name'];
		}else{
			$relative_dir = $file['relative_path'].'/'.$file['name'];
		}
		$query = "DELETE FROM project_files WHERE project_id = '".$file['project_id']."' AND relative_path LIKE '".$relative_dir."%'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
	
	// Select the latest projects, returns a list of the projects on success and false on failure
	public function get_latest_projects($num) {
		$query = "SELECT projects.*, users.username as owner FROM projects JOIN projects_editors ON projects.id = projects_editors.project_id JOIN users ON users.id = projects_editors.user_id WHERE projects_editors.user_type = '1' AND projects.public = '1' ORDER BY projects.id DESC LIMIT ".$num; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the users starting with given username
	public function find_users_like($username) {
		$query = "SELECT username FROM users WHERE username LIKE '".$username."%' LIMIT 10"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the libraries starting with given library name
	public function find_libraries_like($name) {
		$query = "SELECT name FROM libraries WHERE name LIKE '".$name."%' LIMIT 10"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the projects starting with given project name
	public function find_projects_like($name) {
		$query = "SELECT name FROM projects WHERE name LIKE '".$name."%' AND public='1' LIMIT 10"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the project and return data for link
	public function find_project_for_link($name) {
		$project = $this->get_project_name($name); 
		$owner = $this->get_project_owner($project['id']);
		return array('project'=>$project['short_code'],'owner'=>$owner['username']);
	}
	
	// Check if an entry with the filename exists, return true or false
	public function check_lib_exist($filename) {
		$query = "SELECT COUNT(*) FROM libraries WHERE name = '".$filename."'";
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$result = $statement->fetch();
		if($result['COUNT(*)'] > 0){
			return true;
		}else{
			return false;
		}
	}
	
	// Add library into the database
	public function add_library($filename, $owner) {
		$user = $this->get_user_information($owner, 'username');
		$query = "INSERT INTO libraries (name, owner_id) Values('".$filename."','".$user['id']."')"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
	
	// Select the requested number of latest libraries
	public function get_latest_libraries($name,$num,$begin) {
		$query = "SELECT libraries.*, users.username as owner FROM libraries JOIN users ON libraries.owner_id = users.id WHERE name LIKE '".$name."%' ORDER BY libraries.id DESC LIMIT ".$num.",".$begin; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the library by name
	public function get_library($library_name) {
		$query = "SELECT * FROM libraries WHERE name='".$library_name."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Return number of libraries
	public function count_libraries($name) {
		$query = "SELECT COUNT(*) FROM libraries WHERE name LIKE '".$name."%'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$result = $statement->fetch();
		return $result['COUNT(*)'];
	}
	
	// Update file for pending compilation
	public function file_compile_pending($file_id) {
		$query = "UPDATE project_files SET compiled='1' WHERE id='".$file_id."%'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return;
	}
	
	// Update file for pending RE-compilation
	public function file_recompile_prompt($file_id) {
		$query = "UPDATE project_files SET compiled='2' WHERE id='".$file_id."%'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return;
	}
	
	// Select the SID's files, returns a list of the files
	public function get_sid_files($sid) {
		$query = "SELECT * FROM sid_files WHERE sid = '".$sid."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the file by id, returns the file's row on success and false on failure
	public function get_file_byname_sid($file,$sid) {
		$query = "SELECT * FROM sid_files WHERE sid = '".$sid."' AND name='".$file."' "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}	
	
	// Add sid file in database
	public function add_sid_file($name, $sid) {
		$query = "INSERT INTO sid_files (name, sid) Values('".$name."',".$sid." )"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $this->conn->lastInsertId();
	}
	
	// Select the SID file by id, returns the file's row on success and false on failure
	public function get_file_sid($file_id) {
		$query = "SELECT * FROM sid_files WHERE id = '".$file_id."'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetch();
	}
	
	// Update file for pending compilation
	public function file_compile_pending_sid($file_id) {
		$query = "UPDATE sid_files SET compiled='1' WHERE id='".$file_id."%'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return;
	}
	
	// Update SID's file for pending RE-compilation
	public function file_recompile_prompt_sid($file_id) {
		$query = "UPDATE sid_files SET compiled='2' WHERE id='".$file_id."%'"; 
		echo $query;
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return;
	}
	
	// Removes SID file from database, return true or false
	public function remove_file_sid($file_id) {
		$query = "DELETE FROM sid_files WHERE id = '".$file_id."'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute();
	}
}




?>