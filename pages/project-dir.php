<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	if($search_user['id'] == $user->id){
		if(empty($_GET['dir'])){
			$_GET['dir']="/";
		}
		if($_GET['dir']!="/"){
			$_GET['dir'] = "/".$_GET['dir'];
		}
		$files = $db->get_project_files($search_user['id'],$_GET['project'], $_GET['dir']);
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