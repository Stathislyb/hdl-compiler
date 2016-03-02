<?php
	$projects = $db->get_user_projects_public($search_user['id']);
	$proj_num = count($projects);
	$last_row = $proj_num % 3;
	if($last_row!=0){
		$last_row_size = (12 / $last_row)-1;
	}else{
		$last_row_size = 11;
	}
	$j=1;
	if( !empty($projects) ){		
		$i=0;
		echo '<div class="row">';
		foreach ($projects as $project) {
			$thresshold = $last_row+$j;
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
			echo "<p>".$project['description']."</p>";
			echo "</div>";
			$j++;
			
		}
		if($i<=3){
			echo '</div>';
		}
	}else{
		echo "The user has no public projects."; 
	}
?>