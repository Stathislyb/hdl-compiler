<?php
	$projects = $db->get_latest_projects(5);
?>

<div class="list-group">
	<?php 
	foreach($projects as $project){ 
		$description = $gen->fix_string_length($project['description']);
		$link_project = $BASE_URL."/project/".$project['owner']."/".$project['short_code']; 
		$link_owner = "<a href='".$BASE_URL."/project/".$project['owner']."/'> (".$project['owner'].")";
	?>
		<a href="<?php echo $link_project; ?>" class="list-group-item">
			<h4 class="list-group-item-heading">
				<?php echo $project['name']; ?>
				<small> By <?php echo $project['owner']; ?></small>
			</h4>
			<p class="list-group-item-text"><?php echo $description; ?></p>
		</a>
	<?php } ?>
</div>