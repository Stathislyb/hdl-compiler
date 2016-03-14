<?php
	if(isset($_GET['page'])){
		$page=20*($_GET['page']-1);
	}else{
		$page=20*0;
	}
	$libraries = $db->get_latest_libraries($page,19);
	$libraries_num = $db->count_libraries();
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
				echo "<li><a href='".$BASE_URL."/libraries/page/".$j."'>".$j."</a></li>";
				$j++;
			}
		}
		?>
	</ul>
</center>