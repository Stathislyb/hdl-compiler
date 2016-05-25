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
	$projects = $db->get_user_projects($search_user['id']);
	$proj_num = count($projects)+1;
	$last_row = $proj_num % 3;
	if($last_row!=0){
		$last_row_size = (12 / $last_row)-1;
	}else{
		$last_row_size = 11;
	}
	$j=1;
?>
<h2>User Projects</h2>
	<?php 
		$i=0;
		$size='col-sm-3';

		echo '<div class="row">';
		foreach ($projects as $project) {
			$thresshold = $last_row+$j;
			$description = $gen->fix_string_length($project['description']);
			if( $thresshold <= $proj_num){
				$size='col-sm-3';
			}else{
				$size='col-sm-'.$last_row_size." center-tile-".$last_row_size;
			}
			$i++;
			if($i>3){
				echo '</div><div class="row">';
				$i=1;
			}
			
			echo "<div class='".$size." square-tiles' onclick='window.location=\"".$BASE_URL."/project/".$search_user['username']."/".$project['short_code']."\";'>";
			echo "<div class='header'>".$project['name']."</div>";
			echo "<p>".$description."</p>";
			echo "</div>";
			$j++;
		}
		$thresshold = $last_row+$j;
		if( $thresshold <= $proj_num){
			$size='col-sm-3';
		}else{
			$size='col-sm-'.$last_row_size." center-tile-".$last_row_size;
		}
		if($i<=3){
			echo "<div class='".$size." square-tiles' onclick='window.location=\"".$BASE_URL."/create-project/".$search_user['username']."\";'><center>";
			echo "<h3>Create New Project</h3>";
			echo '<span class="glyphicon glyphicon-plus-sign pointer very-strong" aria-hidden="true"></span></center></div></div>';
		}else{
			echo "<div class='row'><div class='".$size." square-tiles' onclick='window.location=\"".$BASE_URL."/create-project/".$search_user['username']."\";'><center>";
			echo "<h3>Create New Project</h3>";
			echo '<span class="glyphicon glyphicon-plus-sign pointer very-strong" aria-hidden="true"></span></center></div></div></div>';
		}
	?>