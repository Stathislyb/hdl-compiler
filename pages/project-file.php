<?php 
	
	$gen = new General;
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['project'], $search_user['id']);
	$editors = $db->get_project_editors($project['id']);
	if($user->validate_edit_rights($editors) || $project['public']==1){
		$file_name = substr(strrchr($_GET['file'], '/'), 1 );
		if(empty($file_name)){
			$file_name = $_GET['file'];
			$dir = $gen->clear_dir("/");
		}else{
			$dir_length = strlen($_GET['file']) - strlen($file_name)-1;
			$dir = substr($_GET['file'],0,$dir_length );
			$dir = $gen->clear_dir($dir);
		}
		//echo $file_name."<br/>";
		//echo $dir."<br/>";
		echo $gen->path_to_links($dir, $search_user['username'], $project['name'], $BASE_URL);
?>
<br/>Made by <?php echo $search_user['username']; ?>

		<div class="topic1" id="listfiles">
			<?php
			$path = $BASE_DIR.$search_user['username']."/".$_GET['project']."/".$_GET['file'];
			if (file_exists($path)){
				edit_file($path);
			}
			echo "<input type='hidden' value='".$path."' id='path' />";
			?>
			<br />
		</div>
	
<br/>
<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>