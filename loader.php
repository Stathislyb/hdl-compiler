<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);

if (session_status() == PHP_SESSION_NONE) {
	session_start();
}

# Set here the variables
# --START--
$BASE="/tmp/VHDL/";
$JOBDIRECTORY="/tmp/jobs/";
$STATUSDIR="/tmp/status/";
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$BASE_URL = $protocol.$_SERVER['SERVER_NAME']."/vhdl";
$BASE_DIR = "/home/user/hdl-compiler/";
$BASE_SID = "/tmp/VHDL/";
# --END--

//Check the existance of the BASE directory and the JOBDIRECTORY
if ( ! file_exists($BASE) ) { mkdir($BASE,0777,true);echo "TEMP1_MKD"; } 
if ( ! file_exists($JOBDIRECTORY) ) { mkdir($JOBDIRECTORY,0777,true); echo "TEMP2_MKD2"; }
if ( ! file_exists($STATUSDIR) ) { mkdir($STATUSDIR,0777,true); echo "TEMP3_MKD3"; }

// Load Classes
require_once('classes/Database.php');
require_once('classes/Messages.php');
require_once('classes/User.php');
require_once('classes/General.php');
// init. objects
$db = new Database;
$messages = new Messages;
$gen = new General;

// login pseudo user with session id
if(isset($_SESSION['SID']) && $_SESSION['SID']>0){
	$_SESSION['vhdl_user']['username'] = "Guest";
	$_SESSION['vhdl_user']['id'] = "0";
	$_SESSION['vhdl_user']['loged_in'] = 1;
	$_SESSION['vhdl_user']['type'] = 0;
}
if( isset($_SESSION['vhdl_user']) ){
	$user = new User($_SESSION['vhdl_user']);
}
if( !isset($_SESSION['vhdl_msg']) ){
	$_SESSION['vhdl_msg'] = array();
}

require_once('post_handler.php');

?>
