<?php
// load necessary files
include('loader.php');

$allowed_action=false;
if( isset($_GET['action']) ){
	if($_GET['action']=="project-file"){
		$allowed_action=true;
	}
}

// display the appropriate (or requested) page for logged in and out users
if( (isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1) || $allowed_action==true ){
	if( isset($_GET['action']) ){
		$included_file = $_GET['action'].'.php';
	}else{
		if($_SESSION['vhdl_user']['id'] == '0'){
			$included_file='loggedin_sid.php';
		}else{
			$included_file='loggedin.php';
		}
	}
}else{
	$included_file='loggedout.php';
}

//bring header and navbar
include('theme/header.php');
// display messages from the user's last interaction
$messages->display_msg($_SESSION['vhdl_msg']);
//bring the requested file
include('pages/'.$included_file);
//bring the footer
include('theme/footer.php');



/*
***********************************
*****  DEBUG MESSAGES BELLOW ******
***********************************
*/
echo "<div class='hidden'>";
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
echo "</div>";
?>
