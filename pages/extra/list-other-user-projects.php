<?php
	$projects = $db->get_user_projects($search_user['id']);
	if( !empty($projects) ){		
		$i=0;
		echo '<div class="row">';
		foreach ($projects as $project) {
			if($project['public']==1){
				$i++;
				if($i>3){
					echo '</div><div class="row">';
					$i=1;
				}

				echo "<div class='col-sm-3 square-tiles' onclick='window.location=\"".$BASE_URL."/project/".$search_user['username']."/".$project['short_code']."\";'>";
				echo "<div class='header'>".$project['name']."</div>";
				echo "<p>".$project['description']."</p>";
				echo "</div>";
							
			}
		}
		if($i<=3){
			echo '</div>';
		}
	}else{
		echo "The user has no public projects."; 
	}
?>