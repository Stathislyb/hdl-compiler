<?php 


 //error_reporting(E_ALL); 
  //ini_set("display_errors", 1);


function connect(){


	$host="127.0.0.1"; // Host name
	$host="localhost"; // Host name 
	$username="root"; // Mysql username 
	$password=""; // Mysql password 
	$db_name="vhdl_compiler"; // Database name 


	try{
		$conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8",$username,$password);
		$conn->exec("SET NAMES 'utf8'");
	}
		catch(PDOException $pe){
		die('Connection error:' . $pe->getmessage());
	}
	return $conn;
}

$conn = connect();
$timer=0;
for($i=1;$i<=1000;$i++){
	$msc = microtime(true);
	$conn->query('DELETE FROM users WHERE id="'.$i.'"');
	$msc = microtime(true)-$msc;
	$timer = ($timer + $msc) / $i;
}
echo "Average InnoDB : ".$timer."<br/>";

$timer=0;
for($i=1;$i<=1000;$i++){
	$msc = microtime(true);
	$conn->query('DELETE FROM users2 WHERE id="'.$i.'"');
	$msc = microtime(true)-$msc;
	$timer = ($timer + $msc) / $i;
}
echo "Average MyISAM : ".$timer;
?>