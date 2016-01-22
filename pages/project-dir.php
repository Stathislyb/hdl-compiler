<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['project'], $search_user['id']);
	$is_editor = $db->verify_editor($project['id'], $user->id);
	if($is_editor || $project['public']==1){
		if(empty($_GET['dir'])){
			$_GET['dir']="/";
		}
		if($_GET['dir']!="/"){
			$_GET['dir'] = "/".$_GET['dir'];
		}
		$files = $db->get_project_files($project['id'], $_GET['dir']);
?>
<tr>
	<td>User</td>
	<td>
		<div class="topic2" id="files">
			<?php require('files.php'); ?>
		</div>
	</td>
</tr>
<tr>
	<td>Step3</td>
	<td>
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
	</td>
</tr>
<tr>
	<td>Step4</td>
	<td>
		<div class="topic2" id="link">
			<?php require('link.php'); ?>
		</div>
	</td>
</tr>
<br/>
<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>