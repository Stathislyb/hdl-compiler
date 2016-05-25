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
	$found_users = $db->get_latest_users($name,$page,19);
	$found_user_num = $db->count_users($name);
?>
<div class="list-group">
	<?php 
	foreach($found_users as $found_user){ 
		$link_library = $BASE_URL."/project/".$found_user['username']; 
	?>
		<a href="<?php echo $link_library; ?>" class="list-group-item">
			<h4 class="list-group-item-heading">
				<?php echo $found_user['username']; ?>
			</h4>
		</a>
	<?php } ?>
</div>
<center>
	<ul class="pagination">
		<?php 
		if($found_user_num > 20){
			$j=1;
			for($i=0;$i<$found_user_num;$i+=20){
				echo "<li><a href='javascript:void(0)' onclick='lib_change_page($j);'>".$j."</a></li>";
				$j++;
			}
		}
		?>
	</ul>
</center>