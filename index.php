
<?php

// load necessary files
include('loader.php');

// login pseudo user with session id
if(isset($_SESSION['PID']) && $_SESSION['PID']>0){
	$_SESSION['vhdl_user']['username'] = "Guest";
	$_SESSION['vhdl_user']['loged_in'] = 1;
}


$user = $_SESSION['vhdl_user'];

// display the appropriate (or requested) page for logged in and out users
if(isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){
	if( isset($_GET['action']) ){
		$included_file = $_GET['action'].'.php';
	}else{
		$included_file='main_loggedin.php';
	}
}else{
	$included_file='main_loggedout.php';
}

include('theme/header.php');
include('pages/'.$included_file);
include('theme/footer.php');

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
