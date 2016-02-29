<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['project'], $search_user['id']);
	$editors = $db->get_project_editors($project['id']);
	if( $user->validate_edit_rights($editors) || $project['public']==1){
		$_GET['dir'] = $gen->clear_dir($_GET['dir']);
		$files = $db->get_project_files($project['id'], $_GET['dir']);
		
		echo $gen->path_to_links($_GET['dir'], $search_user['username'], $project['name'], $BASE_URL);
?>
<br/><br/><br/>
<div class="row">
	<div class="col-sm-3 divider-vertical-right"> 
		<?php echo $project['name']."<br/> Created by ".$search_user['username']; ?>
	</div>
	<div class="col-sm-9"> 
		<?php echo $project['description']; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-xs-3"> 
<?php if( $user->validate_ownership($editors) ){
		echo "<a href='".$BASE_URL."/edit-project/".$project['short_code']."'>Edit Project</a><br/>";
	} ?>
		Owner
		<ul class="list-group">
			<?php
			foreach ($editors as $editor) {
				if($editor['user_type'] == "1"){
					echo "<li class='list-group-item'><a href='".$BASE_URL."/project/".$editor['username']."'>".$editor['username']."</a></li>";
				}
			}
			?>
		</ul>
		Editors
		<ul class="list-group">
			<?php
			foreach ($editors as $editor) {
				if($editor['user_type'] == "0"){
					echo "<li class='list-group-item'><a href='".$BASE_URL."/project/".$editor['username']."'>".$editor['username']."</a></li>";
				}
			}
			?>
		</ul>
	</div>
	<div class="col-xs-9">
		<ul class="list-group">
			<?php
			foreach ($files as $file) {
				if($file['relative_path'] == "/"){
					$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type']."/".$file['name'];
				}else{
					$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type'].$file['relative_path']."/".$file['name'];
				}
				echo "<li class='list-group-item'><a href='".$path."'>".$file['name']."</a>";
				echo "<span class='pull-right'><input type='checkbox' value='1' id='file_".$file['id']."'></span></li>";
			}
			?>
		</ul>

		<br />
		<br />
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