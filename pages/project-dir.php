<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	if($search_user['id'] == $user->id){
		$dir= "/".$_GET['dir'];
		$files = $db->get_project_files($search_user['id'],$_GET['project'], $dir);
?>
<tr>
	<td>
		User
	</td>

	<td>
			<?php 
				echo '<tr><td>Step2</td><td>';
				echo '<div class="topic2" id="files">';
				require('files.php');
				echo '</div></td></tr>';
		
				echo '<tr><td>Step3</td><td><div class="topic1" id="listfiles">';
				
				foreach ($files as $file) {

					echo "<a href='".$BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type'].$file['relative_path']."/".$file['name']."'>".$file['name']."</a>"
					."<br /><br />";

				}
				
				echo '</div></td></tt>';

				echo '<tr><td>Step4</td><td><div class="topic2" id="link">';
				require('link.php');
				echo '</div></td></tr>';
			?>
			<br/>
	</td>
</tr>
<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>