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
	$found_components = $db->get_latest_components_admin($name,$page,19);
	$found_components_num = $db->count_users($name);
	$alter=1;
	if($_SESSION['vhdl_lang']=='gr'){
		$col_offset_1 = '2';
		$col_offset_2 = '4';
		$col_size = '2';
	}else{
		$col_offset_1 = '3';
		$col_offset_2 = '5';
		$col_size = '1';
	}
?>

<div id="components_container">
	<ul class="list-group">
		<?php 
		foreach($found_components as $found_component){ 
			$link_edit = $BASE_URL."/libraries/".$found_component['name'];
			$link_suggestion = $BASE_URL."/library_update/".$found_component['name'];
			$alter = ($alter==1)?0:1;
		?>
			<li class="list-group-item row <?php echo ($alter==1)?"alternative_row":""; ?>">
				<a href="<?php echo $link_edit; ?>" class="col-sm-4">
					<h3 class="list-group-item-heading inline-block">
						<?php echo $found_component['name']; ?>
					</h3>
				</a>
				<?php if($found_component['pending_suggestion']==1){ ?>
					<div class="col-sm-2">
						<a class="btn btn-info" href="<?php echo $link_suggestion ?>"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_1'] ?></a>
					</div>
				<?php } ?>
				<?php if($found_component['approved']==0){ ?>
					<form class="form" action="" method="post" >
						<input type="hidden" name="library_id" value="<?php echo $found_component['id']; ?>" />
						<button type="submit" class="col-sm-offset-<?php echo ($found_component['pending_suggestion']==1)?$col_offset_1:$col_offset_2; ?> col-sm-1 btn btn-success" name="post_action" value="Approve_Component_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_2'] ?></button>
					</form>
				<?php }else{ ?>
					<form class="form" action="" method="post" >
						<input type="hidden" name="library_id" value="<?php echo $found_component['id']; ?>" />
						<button type="submit" class="col-sm-offset-<?php echo ($found_component['pending_suggestion']==1)?$col_offset_1:$col_offset_2; ?> col-sm-1 btn btn-warning" name="post_action" value="Disapprove_Component_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_3'] ?></button>
					</form>
				<?php } ?>
				<form class="form" action="" method="post" onsubmit="confirm_component_removal_admin()" >
					<input type="hidden" name="library_id" value="<?php echo $found_component['id']; ?>" />
					<button type="submit" class="col-sm-offset-1 col-sm-<?php echo $col_size ?> btn btn-danger" name="post_action" value="Remove_Component_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_4'] ?></button>
				</form>
			</li>
		<?php } ?>
	</ul>

	<center>
		<ul class="pagination">
			<?php 
			if($found_components_num > 20){
				$j=1;
				for($i=0;$i<$found_components_num;$i+=20){
					echo "<li><a href='javascript:void(0)' onclick='admin_components_change_page($j);'>".$j."</a></li>";
					$j++;
				}
			}
			?>
		</ul>
	</center>

</div>