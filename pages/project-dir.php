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
	<div class="col-sm-3"> 
		<?php  if( $user->validate_ownership($editors) ){ ?>
			<a href='<?php echo $BASE_URL; ?>/edit-project/<?php echo $project['short_code']; ?>'>
				<button type='button' class='btn btn-primary full-row'>Edit Project</button>
			</a><br/>
			<form action='' method='post' >
				<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
				<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
				<button type="submit" name="post_action" value='Remove_Project' class="btn btn-danger full-row" >Remove Project</button>
			</form><br/>
		<?php } ?>
		<ul class="list-group text-center">
			<li class='list-group-item list-header'>Owner</li>
			<li class='list-group-item'><?php echo "<a href='".$BASE_URL."/project/".$search_user['username']."'>".$search_user['username']."</a>"; ?></li>
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
	<div class="col-sm-9">
		<ul class="list-group no-shadow">
			<li class='list-group-item no-border'>
				<span class='pull-right'><input type="checkbox" id="select_all"></span>
			</li>
		</ul>
		<?php require('pages/extra/list-dirfiles.php'); ?>
		<div class="row">
			<div class="col-sm-4">
				<button type="submit" name="post_action" value='Simulate_Project' class="btn btn-info full-row" id="Simulate_Project">Simulate Project</button>
			</div>
			<div><center>
				<form action='' method='post' id='Selected_Action'>
					<input type="hidden" value="" name="selected_ids" id="selected_ids" />
					<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
					<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
					<button type="submit" name="post_action" value='Compile_Selected' class="btn btn-success" >Compile Selected</button>
					<button type="submit" name="post_action" value='Remove_Selected' class="btn btn-danger" >Remove Selected</button>
				</form>
			</center></div>
		</div>
	</div>
</div>
<br/>

<div class="topic2" id="link">
	<?php require('link.php'); ?>
</div>

<?php if($user->validate_edit_rights($editors)){ ?>
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
<?php } ?>

<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>