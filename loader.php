<?php
$BASE_URL = "http://".$_SERVER['SERVER_NAME']."";
$BASE_DIR = "/home/stathis/hdl-compiler/";

require_once('classes/Database.php');
require_once('classes/Messages.php');
require_once('classes/User.php');


require_once('functions.php');
session_dasygenis();


// init. objects
$db = new Database;
$messages = new Messages;

require_once('post_handler.php');

?>
