<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
if( !isset($user->type) || $user->type != '1' ){
	header("location: ".$BASE_URL); 
	exit();
}
?>

<?php 
$option = ( isset($_GET['option']) && !empty($_GET['option']) )? $_GET['option']:'users';
if(isset($_GET['page'])){
	$page=20*($_GET['page']-1);
}else{
	$page=20*0;
}
if(isset($_POST['name'])){
	$name=$_POST['name'];
}else{
	$name='';
} ?>

<div class="row">
<div class="pseudo-nav col-sm-offset-4 col-sm-4">
	<ul class="pseudo-nav-ul row">
		<li class="pseudo-nav-li col-sm-4 <?php echo ($option=='users')?"pseudo-nav-selected":""; ?>"><a class="pseudo-nav-link" href="<?php echo $BASE_URL; ?>/admin/users" >Users</a></li>
		<li class="pseudo-nav-li col-sm-4 <?php echo ($option=='components')?"pseudo-nav-selected":""; ?>"><a class="pseudo-nav-link" href="<?php echo $BASE_URL; ?>/admin/components" >Components</a></li>
		<li class="pseudo-nav-li col-sm-4 <?php echo ($option=='projects')?"pseudo-nav-selected":""; ?>"><a class="pseudo-nav-link" href="<?php echo $BASE_URL; ?>/admin/projects" >Projects</a></li>
	</ul>
</div>
</div>
<br/>


	<div class="form-group">
		<form action="" method="post" id="filter_users" onsubmit="return false;">
			<input name="name" type="text" value="<?php echo $name; ?>" class="form-control" placeholder="Search User" id="filter_users_input"/>
		</form>
	</div>
	<div id="users_container">
		<?php require('pages/extra/list-all-users_admin.php'); ?>
	</div>
