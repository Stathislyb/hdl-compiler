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
	$found_projects = $db->get_latest_projects_admin($name,$page,19);
	$found_projects_num = $db->count_users($name);
	$alter=1;
	if($_SESSION['vhdl_lang']=='gr'){
		$col_offset = '3';
		$col_size = '2';
	}else{
		$col_offset = '5';
		$col_size = '1';
	}
?>

<div id="projects_container">
	<ul class="list-group">
		<?php 
		foreach($found_projects as $found_project){ 
			$owner = $db->get_project_owner($found_project['id']);
			$link_user = $BASE_URL."/project/".$owner['username']."/".$found_project['short_code']; 
			$link_edit = $BASE_URL."/edit-project/".$owner['username']."/".$found_project['short_code'];
			$alter = ($alter==1)?0:1;
		?>
			<li class="list-group-item row <?php echo ($alter==1)?"alternative_row":""; ?>">
				<a href="<?php echo $link_user; ?>" class="col-sm-4">
					<h3 class="list-group-item-heading inline-block">
						<?php echo $found_project['name']; ?>
					</h3>
				</a>
				<a href="<?php echo $link_edit; ?>" class="col-sm-offset-<?php echo $col_offset ?> col-sm-<?php echo $col_size ?> btn btn-info"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_5'] ?></a>
				<form class="form" action="" method="post" onsubmit="confirm_project_removal_admin()" >
					<input type="hidden" name="project_id" value="<?php echo $found_project['id']; ?>" />
					<button type="submit" class="col-sm-offset-1 col-sm-<?php echo $col_size ?> btn btn-danger" name="post_action" value="Remove_Project_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_4'] ?></button>
				</form>
			</li>
		<?php } ?>
	</ul>

	<center>
		<ul class="pagination">
			<?php 
			if($found_projects_num > 20){
				$j=1;
				for($i=0;$i<$found_projects_num;$i+=20){
					echo "<li><a href='javascript:void(0)' onclick='admin_projects_change_page($j);'>".$j."</a></li>";
					$j++;
				}
			}
			?>
		</ul>
	</center>
</div>