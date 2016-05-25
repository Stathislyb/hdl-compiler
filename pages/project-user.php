<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	if($search_user['id'] != $user->id){
?>

<h2>Projects by <?php echo $search_user['username']; ?></h2>

<?php 
if($user->type == '0'){
	require('pages/extra/list-other-user-projects.php'); 
}else{
	require('pages/extra/list-other-user-projects_admin.php'); 
}
?>

<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>