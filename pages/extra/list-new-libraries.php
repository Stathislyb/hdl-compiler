<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php
	$libraries = $db->get_latest_libraries("",0,5);
?>

<div class="list-group">
	<a href='<?php echo $BASE_URL; ?>/libraries' class="btn btn-primary btn-outline pull-right" role="button">View All Libraries </a>
	<h3>Recent Libraries</h3>
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