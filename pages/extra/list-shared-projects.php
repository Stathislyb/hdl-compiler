<?php
	$shared_projects = $db->get_shared_projects($user->id);
	if( !empty($shared_projects) ){
		
		echo "<h2>Shared with User</h2>";
		
		$i=0;
		echo '<div class="row">';
		foreach ($shared_projects as $shared_project) {
			$description = $gen->fix_string_length($shared_project['description']);
			$i++;
			if($i>3){
				echo '</div><div class="row">';
				$i=1;
			}
			
			echo "<div class='col-sm-3 square-tiles' onclick='window.location=\"".$BASE_URL."/project/".$shared_project['owner']."/".$shared_project['short_code']."\";'>";
			echo "<h3>".$shared_project['name']."</h3>";
			echo "<p>".$description."</p>";
			echo "</div>";
			
		}
		if($i<=3){
			echo '</div>';
		}
	}
?>