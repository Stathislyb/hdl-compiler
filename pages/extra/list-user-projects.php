<?php
	$projects = $db->get_user_projects($user->id);
?>
<h2>User Projects</h2>
	<?php 
		$i=0;
		echo '<div class="row">';
		foreach ($projects as $project) {
			$i++;
			if($i>3){
				echo '</div><div class="row">';
				$i=1;
			}
			
			echo "<div class='col-sm-3 square-tiles' onclick='window.location=\"".$BASE_URL."/project/".$user->username."/".$project['short_code']."\";'>";
			echo "<h3>".$project['name']."</h3>";
			echo "<p>".$project['description']."</p>";
			echo "</div>";
			
		}
		if($i<=3){
			echo "<div class='col-sm-3 square-tiles' onclick='window.location=\"/create-project\";'><center>";
			echo "<h3>Create New Project</h3>";
			echo '<h1>+</h1></center></div></div>';
		}else{
			echo "<div class='row'><div class='col-sm-3 square-tiles' onclick='window.location=\"/create-project\";'><center>";
			echo "<h3>Create New Project</h3>";
			echo '</div></div></div>';
		}
//(<a href='".$BASE_URL."/edit-project/".$project['short_code']."'>EDIT</a>)
	?>