<?php
	$libraries = $db->get_latest_libraries(5,0);
?>

<div class="list-group">
	<?php 
	foreach($libraries as $library){ 
		$link_library = $BASE_URL."/libraries/".$library['name']; 
		//$link_owner = "<a href='".$BASE_URL."/project/".$library['owner']."/'> (".$library['owner'].")";
	?>
		<a href="<?php echo $link_library; ?>" class="list-group-item">
			<h4 class="list-group-item-heading">
				<?php echo $library['name']; ?>
			</h4>
			<p class="list-group-item-text"> Created By <?php echo $library['owner']; ?></p>
		</a>
	<?php } ?>
</div>