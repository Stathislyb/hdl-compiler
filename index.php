
<?php

// load necessary files
include('loader.php');

// init. objects
$db = new Database;
$messages = new Messages;

// confirm log in information (id > 0){if given)
if(isset($_POST["login"]) && $_POST["login"]==1){
	$id = $db->confirm_user($_POST["username"],$_POST["password"]);
	if($id > 0){
		$_SESSION['vhdl_user']['username'] = $_POST["username"];
		$_SESSION['vhdl_user']['id'] = $id;
		$_SESSION['vhdl_user']['loged_in'] = 1;
		array_push($_SESSION['vhdl_msg'],"success_login");
	}else{
		array_push($_SESSION['vhdl_msg'],"fail_login");
	}
}
// login pseudo user with session id
if(isset($_SESSION['PID']) && $_SESSION['PID']>0){
	$_SESSION['vhdl_user']['username'] = "Guest";
	$_SESSION['vhdl_user']['loged_in'] = 1;
}

// log user out (if requested)
if(isset($_POST["logout"]) && $_POST["logout"]==1){
	unset($_SESSION['vhdl_user']);
	unset($_SESSION['PID']);
	setcookie("PID", 0, time()-3600);
}

// register user
if(isset($_POST["register"]) && $_POST["register"]==1){
	if(filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) && strlen($_POST["email"])<=50 ) {
		if($_POST["password"] == $_POST["password_confirm"]){
			if($db->register_user($_POST["username"],$_POST["password"],$_POST["email"])){
				array_push($_SESSION['vhdl_msg'], 'success_register');	
			}else{
				array_push($_SESSION['vhdl_msg'], 'fail_register');
			}
		}else{
			array_push($_SESSION['vhdl_msg'], 'fail_register_confirm');	
		}
	}else{
		array_push($_SESSION['vhdl_msg'], 'invalid_mail');
	}
}

$user = $_SESSION['vhdl_user'];

// display the appropriate home page for logged in and out users
if(isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){
	include('theme/header.php');
	include('pages/main_loggedin.php');
	include('theme/footer.php');
}else{
	include('theme/header.php');
	include('pages/main_loggedout.php');
	include('theme/footer.php');
}

// display messages from the user's last interaction
$messages->display_msg($_SESSION['vhdl_msg']);
if(isset($_GET)){
	echo "<br/>GET :<br/>";
	var_dump($_GET);
	echo "<br/>";
}
if(isset($_POST)){
	echo "<br/>POST :<br/>";
	var_dump($_POST);
	echo "<br/>";
}
if(isset($_SESSION)){
	echo "<br/>SESSION :<br/>";
	var_dump($_SESSION);
	echo "<br/>";
}

?>
