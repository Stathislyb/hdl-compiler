<?php
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$BASE_URL = $protocol.$_SERVER['SERVER_NAME']."/vhdl";
$BASE_DIR = "/home/user/hdl-compiler/";
$BASE_SID = "/tmp/VHDL/";

require_once('classes/Database.php');
require_once('classes/Messages.php');
require_once('classes/User.php');
require_once('classes/General.php');

require_once('functions.php');
session_dasygenis();


// init. objects
$db = new Database;
$messages = new Messages;
$gen = new General;

// login pseudo user with session id
if(isset($_SESSION['SID']) && $_SESSION['SID']>0){
	$_SESSION['vhdl_user']['username'] = "Guest";
	$_SESSION['vhdl_user']['id'] = "0";
	$_SESSION['vhdl_user']['loged_in'] = 1;
}

if( isset($_SESSION['vhdl_user']) ){
	$user = new User($_SESSION['vhdl_user']);
}

if( !isset($_SESSION['vhdl_msg']) ){
	$_SESSION['vhdl_msg'] = array();
}

require_once('post_handler.php');

?>
