<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['project'], $search_user['id']);
	$editors = $db->get_project_editors($project['id']);
	if( $user->validate_edit_rights($editors) || $project['public']==1){
		$_GET['dir'] = $gen->clear_dir($_GET['dir']);
		$files = $db->get_project_files($project['id'], $_GET['dir']);
		
		if($_SESSION['vhdl_user']['username'] == "Guest"){
			$upload_dir=$BASE.$_SESSION['PID']."/"; 
		}else{
			if($_GET['dir']=='/'){
				$upload_dir=$BASE_DIR.$search_user['username']."/".$project['short_code']."/"; 
			}else{
				$upload_dir=$BASE_DIR.$search_user['username']."/".$project['short_code'].$_GET['dir']."/"; 
			}
		}

		
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
			echo "<a href='".$BASE_URL."/edit-project/".$project['short_code']."'>".
				"<button type='button' class='btn btn-primary full-row'>Edit Project</button>".
				"</a><br/>";
		} ?>
		<ul class="list-group text-center">
			<li class='list-group-item list-header'>Owner</li>
			<?php echo $search_user['username']; ?>
		</ul>
		<ul class="list-group text-center">
			<li class='list-group-item list-header'>Editors</li>
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
		<?php require('pages/extra/list-dirfiles.php'); ?>
	</div>
</div>
<br/>

<div class="topic2" id="link">
	<?php require('link.php'); ?>
</div>

<!-- Modal -->
<div id="Create-filedir-Modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
		  <!-- Modal header menu-->
        <h3>Add File or Directory</h3>
      </div>
      <div class="modal-body tab-content">
		  <!-- Modal Body Directory-->
		<form action='' method='post'>
			<h4>Create Directory</h4>
			<span class="row">
				<span class="col-sm-6">
					<input type="text" name="dir_name" size='15' placeholder="Directory Name"/>
					<input type="hidden" value="<?php echo $_GET['project']; ?>" name="project_shortcode">
					<input type="hidden" value="<?php echo $_GET['dir']; ?>" name="current_dir">
					<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
				</span>
				<span class="col-sm-6">
					<button type="submit" name="post_action" value='Create_dir' class="btn btn-default">Create Directory</button>
				</span>
			</span>
		</form>
		<br/>
	   	<!-- Modal Body Create File-->
		<form action='' method='post'>
			<h4>Create File</h4>
			<span class="row">
				<span class="col-sm-6">
					<input type="text" name="file_name" size='15' placeholder="File Name"/>
					<input type="hidden" value="<?php echo $_GET['project']; ?>" name="project_shortcode">
					<input type="hidden" value="<?php echo $_GET['dir']; ?>" name="current_dir">
					<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
				</span>
				<span class="col-sm-6">
					<button type="submit" name="post_action" value='Create_file' class="btn btn-default">Create File</button>
				</span>
			</span>
		</form>
		<br/>
		<!-- Modal Body Upload File-->
		<form enctype="multipart/form-data" action="" method="post">
			<h4>Upload File</h4>
			<span class="row">
				<span class="col-sm-6">
					<input name="userfile" type="file" />
					<input type="hidden" name="MAX_FILE_SIZE" value="32000000" />
					<input type="hidden" name="upload_dir" value="<?php echo $upload_dir; ?>" />
					<input type="hidden" value="<?php echo $_GET['project']; ?>" name="project_shortcode">
					<input type="hidden" value="<?php echo $_GET['dir']; ?>" name="current_dir">
					<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
				</span>
				<span class="col-sm-6">
					<button type="submit" name="post_action" value='Upload_File' class="btn btn-default">Upload File</button>
				</span>
			</span>
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>