<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php
	$libraries = $db->get_latest_libraries($name,$page,19);
	$libraries_num = $db->count_libraries($name);
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
<center>
	<ul class="pagination">
		<?php 
		if($libraries_num > 20){
			$j=1;
			for($i=0;$i<$libraries_num;$i+=20){
				echo "<li><a href='javascript:void(0)' onclick='lib_change_page($j);'>".$j."</a></li>";
				$j++;
			}
		}
		?>
	</ul>
</center>