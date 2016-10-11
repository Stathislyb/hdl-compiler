<?php

class Database {
	// Database class variables
	public $conn;
	
	// Class constractor function
	//  initialise the PDO connection
	function __construct(){
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
	function __destruct(){
		$this->conn = null;
	}
	
	// Confirm username and password
	//  on success returns user ID, else 0
	function confirm_user($username, $password) {
		$password = md5($password);
		$query = "SELECT * FROM users WHERE username = :username"; 
		$statement = $this->conn->prepare($query); 
		
		if( $statement->execute(array(':username'=>$username)) ){
			$result = $statement->fetch();
			if($result['password'] == $password){
				return $result['id'];
			}
		}
		return 0;
	}
	
	// Select and return user's theme
	function get_user_theme($id) {
		$query = "SELECT * FROM users WHERE id = :id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':id'=>$id));
		$result = $statement->fetch();
		return $result['theme'];
	}
	
	// Update the user's theme
	function update_user_theme($id, $theme) {
		$query = "UPDATE users SET theme= :theme WHERE id = :id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':theme'=>$theme,':id'=>$id));
	}
	
	// Return 1 if user is admin, 0 otherwise
	function user_type($id) {
		$query = "SELECT * FROM users WHERE id = :id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':id'=>$id));
		$result = $statement->fetch();
		return $result['type'];
	}
	
	// Return 1 if user is activated, 0 otherwise
	function is_user_active($id) {
		$query = "SELECT * FROM users WHERE id = :id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':id'=>$id));
		$result = $statement->fetch();
		return $result['activated'];
	}
	
	// Return true if user is successfully activated, false otherwise
	function activate_user($id, $code) {
		$query = "SELECT * FROM user_activation WHERE user_id = :id AND activ_code= :activ_code"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':id'=>$id,':activ_code'=>$code));
		if($statement->rowCount()==1){
			$query = "UPDATE users SET activated='1' WHERE id = :id "; 
			$statement = $this->conn->prepare($query); 
			$statement->execute(array(':id'=>$id));
			$query = "DELETE FROM user_activation WHERE user_id = :id "; 
			$statement = $this->conn->prepare($query); 
			$statement->execute(array(':id'=>$id));
			return true;
		}else{
			return false;
		}
	}
	
	// Return true if the username is taken, else false
	function taken_username($username) {
		$query = "SELECT * FROM users WHERE username = :username"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':username'=>$username));
		if($statement->rowCount()>0 && $username!="libraries" && $username!="library_updates"){
			return true;
		}else{
			return false;
		}
	}
	
	// Register user in database, returns id on success and 0 on failure
	function register_user($username, $password, $email, $phone, $code, $active, $type){
		$password = md5($password);
		$query = "INSERT INTO users (username, password, email, telephone, activated, type) Values(:username,:password,:email,:phone,:active,:type)"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':username'=>$username,':password'=>$password,':email'=>$email,':phone'=>$phone,':active'=>$active,':type'=>$type));
		$user_id = $this->conn->lastInsertId();
		if($user_id > 0){
			$query = "INSERT INTO user_activation (user_id, activ_code) Values(:user_id,:code)"; 
			$statement = $this->conn->prepare($query); 
			$statement->execute(array(':user_id'=>$user_id,':code'=>$code));
		}
		return $user_id;
	}
	
	// Update the user's settings
	function edit_user($pass,$phone,$email,$theme,$id,$space){
		if($pass != NULL){
			$pass_query = "password='".md5($pass)."', ";
		}else{
			$pass_query = " ";
		}
		if($phone != NULL){
			$phone_query = "telephone='".$phone."', ";
		}else{
			$phone_query = " ";
		}
		if($space != NULL){
			$space_query = "available_space='".$space."', ";
		}else{
			$space_query = " ";
		}
		$query = "UPDATE users SET ".$pass_query." ".$phone_query." ".$space_query." email= :email, theme= :theme WHERE id = :id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':email'=>$email,':theme'=>$theme,':id'=>$id));
	}
	
	// Select the user's projects, returns a list of the projects on success and false on failure
	function get_user_projects($user_id) {
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = :user_id AND user_type='1'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':user_id'=>$user_id));
		return $statement->fetchAll();
	}
	
	// Select the user's public projects, returns a list of the projects on success and false on failure
	function get_user_projects_public($user_id) {
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = :user_id AND user_type='1' AND public='1'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':user_id'=>$user_id));
		return $statement->fetchAll();
	}
	
	// Select the projects in which the user is an editor but not the owner, returns a list of the projects on success and false on failure
	function get_shared_projects($user_id) {
		$query = "SELECT projects.* FROM projects_editors INNER JOIN projects ON projects_editors.project_id = projects.id WHERE projects_editors.user_id = :user_id AND user_type='0'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':user_id'=>$user_id));
		$projects = $statement->fetchAll();
		foreach ($projects as $key=>$project) {
			$query = "SELECT users.username FROM projects_editors INNER JOIN users ON projects_editors.user_id = users.id WHERE projects_editors.project_id = :project_id AND user_type='1'"; 
			$statement = $this->conn->prepare($query); 
			$statement->execute(array(':project_id'=>$project['id']));
			$owner = $statement->fetch();
			$projects[$key]['owner'] = $owner['username'] ;
		}
		return $projects;
	}
	
	// Select the user's information by search parameter
	function get_user_information($user, $search_type) {
		$query = "SELECT * FROM users WHERE  ".$search_type." = :user "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array('user' => $user));
		return $statement->fetch();
	}
	
	// Select project by id, returns the project on success and false on failure
	function get_project($project_id) {
		$query = "SELECT * FROM projects WHERE id= :project_id "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':project_id'=>$project_id));
		return $statement->fetch();
	}
	
	// Select project by short-code, returns the project on success and false on failure
	function get_project_shortcode($project_code, $user_id) {
		$query = "SELECT projects.* FROM projects JOIN projects_editors ON projects_editors.project_id = projects.id WHERE projects_editors.user_id= :user_id AND projects.short_code = :project_code"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':user_id'=>$user_id, ':project_code'=>$project_code));
		return $statement->fetch();
	}
	
	// Select project by name
	function get_project_name($project_name) {
		$query = "SELECT * FROM projects WHERE name= :project_name"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':project_name'=>$project_name));
		return $statement->fetch();
	}
	
	// Select project's owner and return his username
	function get_project_owner($project_id) {
		$query = "SELECT * FROM projects JOIN projects_editors ON projects_editors.project_id = projects.id JOIN users ON users.id = projects_editors.user_id WHERE projects.id= :project_id AND projects_editors.user_type = '1'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':project_id'=>$project_id));
		return $statement->fetch();
	}
	
	// Select project editors by project id, returns the project's editors on success and false on failure
	function get_project_editors($project_id) {
		$query = "SELECT users.username,projects_editors.user_type FROM users JOIN projects_editors ON projects_editors.user_id = users.id WHERE projects_editors.project_id = :project_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':project_id'=>$project_id));
		return $statement->fetchAll();
	}
	
	// Create new project, returns id on success and 0 on failure
	function create_project($name, $description, $short_code, $public) {
		$query = "INSERT INTO projects (name, description, short_code, public) Values(:name,:description,:short_code,:public)"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':name'=>$name, ':description'=>$description, ':short_code'=>$short_code, ':public'=>$public));
		return $this->conn->lastInsertId();
	}
	
	// Create new project, returns id on success and 0 on failure
	function remove_project($project_id) {
		$query = "DELETE FROM projects WHERE id = :project_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':project_id'=>$project_id));
		$query = "DELETE FROM projects_editors WHERE project_id= :project_id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':project_id'=>$project_id));
	}
	
	// Create new project, returns id on success and 0 on failure
	function clear_project($project_id) {
		$query = "DELETE FROM project_files WHERE project_id= :project_id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':project_id'=>$project_id));
	}
	
	// Add editor/owner to project, returns id on success and 0 on failure
	function add_project_editor($username, $project_id, $user_type) {
		$query = "SELECT id FROM users WHERE username = :username"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':username'=>$username));
		$user = $statement->fetch();
		$query = "INSERT INTO projects_editors (user_id, project_id, user_type) Values(:user_id,:project_id,:user_type)"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':user_id'=>$user['id'], ':project_id'=>$project_id, ':user_type'=>$user_type));
		return $this->conn->lastInsertId();
	}
	
	// Add multiple editors to the project, returns true on success and flase on failure
	function add_project_multi_editors($users, $project_id) {
		$users_array = explode(",", $users);
		
		$query_remove_old = "DELETE FROM projects_editors WHERE user_type = '0' AND project_id = :project_id "; 
		$statement = $this->conn->prepare($query_remove_old); 
		$statement->execute(array(':project_id'=>$project_id));
		
		$query_verify = "SELECT user_id FROM projects_editors WHERE user_type = '1' AND project_id = :project_id "; 
		$statement = $this->conn->prepare($query_verify); 
		$statement->execute(array(':project_id'=>$project_id));
		$result = $statement->fetch();
		
		foreach($users_array as $user){
			$query = "SELECT id FROM users WHERE username = :user"; 
			$statement = $this->conn->prepare($query); 
			$statement->execute(array(':user'=>$user));
			$user_res = $statement->fetch();
			
			if($result['user_id'] != $user_res['id']){
				$query_insert = "INSERT INTO projects_editors (user_id, project_id, user_type) Values(:user_id,:project_id,'0')"; 
				$statement = $this->conn->prepare($query_insert); 
				$statement->execute(array(':user_id'=>$user_res['id'], ':project_id'=>$project_id));
			}
		}
				
		return;
	}
	
	// Create new project, returns true on success and false on failure
	function edit_project($name, $description, $short_code, $project_id, $share_project) {
		$query = "UPDATE projects SET name=:name, description= :description, short_code= :short_code, public= :public WHERE projects.id= :project_id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':name'=>$name, ':description'=>$description, ':short_code'=>$short_code, ':public'=>$share_project, ':project_id'=>$project_id));
	}
	
	// Select the project's files, returns a list of the project's files
	function get_project_files($project_id) {
		$query = "SELECT * FROM project_files WHERE project_id = :project_id ORDER BY id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':project_id'=>$project_id));
		return $statement->fetchAll();
	}
	
	// Select the file by id, returns the file's row on success and false on failure
	function get_file($file_id) {
		$query = "SELECT * FROM project_files WHERE id = :file_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':file_id'=>$file_id));
		return $statement->fetch();
	}
	
	// Select the file by id, returns the file's row on success and false on failure
	function get_file_byname($file,$project) {
		$query = "SELECT * FROM project_files WHERE project_id = :project AND name= :file "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':project'=>$project, ':file'=>$file));
		return $statement->fetch();
	}
	
	// Add file in database
	function add_file($dir_name, $project_id) {
		$query = "INSERT INTO project_files (name, project_id) Values(:dir_name, :project_id)"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':dir_name'=>$dir_name, ':project_id'=>$project_id));
		return $this->conn->lastInsertId();
	}
	
	// Add file from library in database
	function add_file_component($library, $project_id) {
		$query = "INSERT INTO project_files (name, project_id,component,version) Values(:library_name, :project_id, :library_id, :library_vers)"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':library_name'=>$library['name'], ':project_id'=>$project_id, ':library_id'=>$library['id'], ':library_vers'=>$library['version']));
		return $this->conn->lastInsertId();
	}
	
	// Update file from library in database
	function update_file_component($library, $project_id) {
		$query = "UPDATE project_files SET version = :library_vers WHERE project_id = :project_id AND component = :library_id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':library_vers'=>$library['version'], ':project_id'=>$project_id, ':library_id'=>$library['id']));
	}
	
	// Check if a file/dir exists already, return true or false
	function check_file_exist($name, $project_id) {
		$query = "SELECT COUNT(*) FROM project_files WHERE project_id = :project_id AND name = :name "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':name'=>$name, ':project_id'=>$project_id));
		$result = $statement->fetch();
		if($result['COUNT(*)'] > 0){
			return true;
		}else{
			return false;
		}
	}
	
	// Removes file from database, return true or false
	function remove_file($file_id) {
		$query = "DELETE FROM project_files WHERE id = :file_id "; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':file_id'=>$file_id));
	}
	
	// Select the latest projects, returns a list of the projects on success and false on failure
	function get_latest_projects($num) {
		$query = "SELECT projects.*, users.username as owner FROM projects JOIN projects_editors ON projects.id = projects_editors.project_id JOIN users ON users.id = projects_editors.user_id WHERE projects_editors.user_type = '1' AND projects.public = '1' ORDER BY projects.id DESC LIMIT ".$num; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the users starting with given username
	function find_users_like($username) {
		$query = "SELECT username FROM users WHERE username LIKE '".$username."%' LIMIT 10"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the requested number of latest users
	function get_latest_users_admin($name,$num,$begin) {
		$query = "SELECT * FROM users WHERE username LIKE '".$name."%' ORDER BY id DESC LIMIT ".$num.", ".$begin; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	// Select the requested number of latest components
	function get_latest_components_admin($name,$num,$begin) {
		$query = "SELECT * FROM libraries WHERE name LIKE '".$name."%' ORDER BY approved ASC, pending_suggestion DESC, id ASC LIMIT ".$num.", ".$begin; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	// Select the requested number of latest projects
	function get_latest_projects_admin($name,$num,$begin) {
		$query = "SELECT * FROM projects WHERE name LIKE '".$name."%' ORDER BY id DESC LIMIT ".$num.", ".$begin; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Return number of users
	function count_users($name) {
		$query = "SELECT COUNT(*) FROM users WHERE username LIKE '%".$name."%' "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$result = $statement->fetch();
		return $result['COUNT(*)'];
	}
	
	// Select the libraries starting with given library name
	function find_libraries_like($name) {
		$query = "SELECT name FROM libraries WHERE name LIKE '%".$name."%' AND approved='1' LIMIT 10"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the projects starting with given project name
	function find_projects_like($name) {
		$query = "SELECT name FROM projects WHERE name LIKE '%".$name."%' AND public='1' LIMIT 10"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the project and return data for link
	function find_project_for_link($name) {
		$project = $this->get_project_name($name); 
		$owner = $this->get_project_owner($project['id']);
		return array('project'=>$project['short_code'],'owner'=>$owner['username']);
	}
	
	// Check if an entry with the filename exists, return true or false
	function check_lib_exist($filename) {
		$query = "SELECT COUNT(*) FROM libraries WHERE name = :filename";
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':filename'=>$filename));
		$result = $statement->fetch();
		if($result['COUNT(*)'] > 0){
			return true;
		}else{
			return false;
		}
	}
	
	// Add library into the database
	function add_library($filename, $owner, $file_id) {
		$user = $this->get_user_information($owner, 'username');
		$query = "INSERT INTO libraries (name, owner_id, file_id) Values(:filename, :user_id, ;file_id)"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':filename'=>$filename, ':user_id'=>$user['id'], ':file_id'=>$file_id));
		return $this->conn->lastInsertId();
	}
	
	// Add update suggestion for library into the database
	function suggest_update_library($lib_id, $name) {
		$query = "UPDATE libraries SET pending_suggestion='1' WHERE id = :lib_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':lib_id'=>$lib_id)); 
		$query = "INSERT INTO library_updates (lib_id, library) Values(:lib_id, :name)"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':lib_id'=>$lib_id, ':name'=>$name));
		return $this->conn->lastInsertId();
	}
	
	// Select update suggestion by library id
	function get_library_suggestion($library_id) {
		$query = "SELECT * FROM library_updates WHERE lib_id = :library_id "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':library_id'=>$library_id));
		return $statement->fetch();
	}
	
	// Remove update suggestion from database
	function remove_library_suggestion($library_id) {
		$query = "UPDATE libraries SET pending_suggestion='0' WHERE id = :library_id "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':library_id'=>$library_id));		
		$query = "DELETE FROM library_updates WHERE lib_id = :library_id "; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':library_id'=>$library_id));
	}
	
	// Increase the library's version by 1
	function increase_library_version($lib_id) {
		$library=$this->get_library_id($lib_id);
		$new_version = $library['version']+1;
		$query = "UPDATE libraries SET version = :new_version WHERE id = :library_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':new_version'=>$new_version, ':library_id'=>$library_id));
		if($statement->rowCount()==1){
			return true;
		}else{
			return false;
		}
	}
	
	// Select the requested number of latest libraries
	function get_latest_libraries($name,$num,$begin) {
		$query = "SELECT libraries.*, users.username as owner FROM libraries JOIN users ON libraries.owner_id = users.id WHERE name LIKE '".$name."%' AND libraries.approved='1' ORDER BY libraries.id DESC LIMIT ".$num.", ".$begin; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		return $statement->fetchAll();
	}
	
	// Select the library by name
	function get_library($library_name,$type) {
		if($type==1){
			$approved="";
		}else{
			$approved="AND approved='1'";
		}
		$query = "SELECT * FROM libraries WHERE name = :library_name ".$approved; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':library_name'=>$library_name));
		return $statement->fetch();
	}
	
	// Select the library by id
	function get_library_id($library_id) {
		$query = "SELECT * FROM libraries WHERE id = :library_id "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':library_id'=>$library_id));
		return $statement->fetch();
	}
	
	// Select the library by file id
	function get_library_file_id($file_id) {
		$query = "SELECT * FROM libraries WHERE file_id = :file_id "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':file_id'=>$file_id));
		return $statement->fetch();
	}
	
	// Return number of libraries
	function count_libraries($name) {
		$query = "SELECT COUNT(*) FROM libraries WHERE name LIKE '".$name."%' AND approved='1'"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute();
		$result = $statement->fetch();
		return $result['COUNT(*)'];
	}
	
	// Check if it's a new library or update
	function is_new_library($new_file_id){
		$query = "SELECT COUNT(*) FROM libraries WHERE file_id = :new_file_id";
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':new_file_id'=>$new_file_id));
		$result = $statement->fetch();
		if($result['COUNT(*)'] == 0){
			return true;
		}else{
			return false;
		}
	}
	
	// Approve library
	function approve_library_admin($lib_id) {
		$query = "UPDATE libraries SET approved='1' WHERE id = :lib_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':lib_id'=>$lib_id));
		if($statement->rowCount()==1){
			return true;
		}else{
			return false;
		}
	}
	
	// Disapprove library
	function disapprove_library_admin($lib_id) {
		$query = "UPDATE libraries SET approved='0' WHERE id = :lib_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':lib_id'=>$lib_id));
		if($statement->rowCount()==1){
			return true;
		}else{
			return false;
		}
	}
	
	// Remove library from database
	function remove_library($lib_id) {
		$query = "DELETE FROM libraries WHERE id = :lib_id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':lib_id'=>$lib_id));
	}
	
	// Update file for pending compilation
	function file_compile_pending($file_id) {
		$query = "UPDATE project_files SET compiled='1' WHERE id = :file_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':file_id'=>$file_id));
		return;
	}
	
	// Update file for pending RE-compilation
	function file_recompile_prompt($file_id) {
		$query = "UPDATE project_files SET compiled='2' WHERE id = :file_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':file_id'=>$file_id));
		return;
	}
	
	// Select the SID's files, returns a list of the files
	function get_sid_files($sid) {
		$query = "SELECT * FROM sid_files WHERE sid = :sid"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':sid'=>$sid));
		return $statement->fetchAll();
	}
	
	// Select the file by id, returns the file's row on success and false on failure
	function get_file_byname_sid($file,$sid) {
		$query = "SELECT * FROM sid_files WHERE sid = :sid AND name = :file "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':sid'=>$sid, ':file'=>$file));
		return $statement->fetch();
	}	
	
	// Add sid file in database
	function add_sid_file($name, $sid) {
		$query = "INSERT INTO sid_files (name, sid) Values(:name, :sid )"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':name'=>$name, ':sid'=>$sid));
		return $this->conn->lastInsertId();
	}
	
	// Select the SID file by id, returns the file's row on success and false on failure
	function get_file_sid($file_id) {
		$query = "SELECT * FROM sid_files WHERE id = :file_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':file_id'=>$file_id));
		return $statement->fetch();
	}
	
	// Update file for pending compilation
	function file_compile_pending_sid($file_id) {
		$query = "UPDATE sid_files SET compiled='1' WHERE id = :file_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':file_id'=>$file_id));
		return;
	}
	
	// Update SID's file for pending RE-compilation
	function file_recompile_prompt_sid($file_id) {
		$query = "UPDATE sid_files SET compiled='2' WHERE id = :file_id"; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':file_id'=>$file_id));
		return;
	}
	
	// Removes SID file from database, return true or false
	function remove_file_sid($file_id) {
		$query = "DELETE FROM sid_files WHERE id = :file_id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':file_id'=>$file_id));
	}
	
	// Remove user from the editor's list of all projects
	function remove_user_editor($user_id) {
		$query = "DELETE FROM user_activation WHERE user_id = :user_id "; 
		$statement = $this->conn->prepare($query); 
		$statement->execute(array(':user_id'=>$user_id));
		$query = "DELETE FROM projects_editors WHERE user_id = :user_id AND user_type='0'"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':user_id'=>$user_id));
	}
	
	// Remove user from the database
	function remove_user($user_id) {
		$query = "DELETE FROM users WHERE id = :user_id"; 
		$statement = $this->conn->prepare($query); 
		return $statement->execute(array(':user_id'=>$user_id));
	}
	
}




?>