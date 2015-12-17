<?php
require_once('classes/Database.php');
require_once('classes/Messages.php');

// init. objects
$db = new Database;
$messages = new Messages;


require_once('functions.php');
session_dasygenis();

require_once('post_handler.php');

?>
