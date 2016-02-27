<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	if($search_user['id'] != $user->id){
?>

<h2>Projects by <?php echo $search_user['username']; ?></h2>

<?php require('pages/extra/list-other-user-projects.php'); ?>

<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>