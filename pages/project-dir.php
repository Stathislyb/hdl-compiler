<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['project'], $search_user['id']);
	$editors = $db->get_project_editors($project['id']);
	if( $user->validate_edit_rights($editors) || $project['public']==1){
		$_GET['dir'] = $gen->clear_dir($_GET['dir']);
		$files = $db->get_project_files($project['id'], $_GET['dir']);
		
		echo $gen->path_to_links($_GET['dir'], $search_user['username'], $_GET['project'], $BASE_URL);
		
?>

<br/>
Created by <?php echo $search_user['username']; ?>

<h2>Files</h2>
		<div class="topic1" id="listfiles">
			<?php
			foreach ($files as $file) {
				if($file['relative_path'] == "/"){
					$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type']."/".$file['name'];
				}else{
					$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type'].$file['relative_path']."/".$file['name'];
				}
				echo "<a href='".$path."'>".$file['name']."</a>"
				."<br /><br />";

			}
			?>
			<br />
			<form action='' method='post'>
				File / Directory Name: <input type="text" name="dirfile_name" size='15' />
				<br />
				<input type="hidden" value="<?php echo $_GET['project']; ?>" name="project_shortcode">
				<input type="hidden" value="<?php echo $_GET['dir']; ?>" name="current_dir">
				<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
				<button type="submit" name="post_action" value='Create_dir'>Create Directory</button>
				<button type="submit" name="post_action" value='Create_file'>Create File</button>
			</form>
		</div>

<br/><br/><br/>
		<div class="topic2" id="files">
			<?php require('files.php'); ?>
		</div>
		<div class="topic2" id="link">
			<?php require('link.php'); ?>
		</div>
<br/>
<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>