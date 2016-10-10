<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['project'], $search_user['id']);
	$editors = $db->get_project_editors($project['id']);
	$is_editor = $user->validate_edit_rights($editors);
	$is_compiled = false;
	if( $is_editor || $project['public']==1){
		$files = $db->get_project_files($project['id']);
		
		if($_SESSION['vhdl_user']['username'] == "Guest"){
			$current_dir=$BASE.$_SESSION['SID']."/"; 
		}else{
			$current_dir=$BASE_DIR.$search_user['username']."/".$project['short_code']."/"; 
		}
		
		$vcd_files = glob($current_dir."*.vcd");
		
		//we want here to display all valid architectures
		$command="cd ".$current_dir.";/usr/lib/ghdl/bin/ghdl -d | grep architecture | awk ' {print $4,$2} '";
		$shell_output=shell_exec($command);
		//Extract the lines
		$architectures = explode(PHP_EOL, $shell_output);
		
		//user link
		echo "<b><a href='".$BASE_URL."/project/".$search_user['username']."'>".$search_user['username']."> </a></b>";
		//project link
		echo "<a href='".$BASE_URL."/project/".$search_user['username']."/".$project['short_code']."'>".$project['name']."/ </a>";
?>
<br/><br/><br/>
<div class="row">
	<div class="col-sm-3 divider-vertical-right"> 
		<?php echo $project['name']."<br/> ".$messages->text[$_SESSION['vhdl_lang']]['project_dir_1']." ".$search_user['username']; ?>
	</div>
	<div class="col-sm-9"> 
		<?php echo $project['description']; ?>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-3"> 
		<?php  if( $is_editor ){ ?>
			<button type="submit" name="post_action" value='Download_Project' class="btn btn-info full-row" data-toggle="modal" data-target="#Download-Project-Modal" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_5'] ?></button>
			<br/>
		<?php  } ?>
		<?php  if( $user->validate_ownership($editors) ){ ?>
			<a href='<?php echo $BASE_URL; ?>/edit-project/<?php echo $search_user['username']; ?>/<?php echo $project['short_code']; ?>'>
				<button type='button' class='btn btn-primary full-row'><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_6'] ?></button>
			</a><br/>
			<form action='' method='post' id="Remove_Project_form" >
				<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
				<button type="submit" name="post_action" value='Remove_Project' class="btn btn-danger full-row" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_7'] ?></button>
			</form><br/>
		<?php } ?>
		<ul class="list-group text-center">
			<li class='list-group-item list-header'><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_8'] ?></li>
			<li class='list-group-item'><?php echo "<a href='".$BASE_URL."/project/".$search_user['username']."'>".$search_user['username']."</a>"; ?></li>
		</ul>
		<ul class="list-group text-center">
			<li class='list-group-item list-header'><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_9'] ?></li>
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
		<?php if( $is_editor ){ ?>
			<ul class="list-group no-shadow">
				<li class='list-group-item no-border'>
					<span class='pull-right'><input type="checkbox" id="select_all"></span>
				</li>
			</ul>
		<?php } ?>
		<?php require('pages/extra/list-dirfiles.php'); ?>
		<?php if( $is_editor ){ ?>
			<div class="row">
				<div class="col-sm-12 space-top-10">
					<form action='' method='post' id='Selected_Action'>
						<input type="hidden" value="" name="selected_ids" id="selected_ids" />
						<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
						<button type="submit" name="post_action" value='Post_Library_Selected' class="btn btn-info" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_10'] ?></button>
						<button type="submit" name="post_action" value='Compile_Selected' class="btn btn-success" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_11'] ?></button>
						<button type="submit" name="post_action" value='Remove_Selected' class="btn btn-danger" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_12'] ?></button>
					</form>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
<hr/>
<?php if( $is_editor && $is_compiled ){ ?>
	<div class="row">
		<div class="col-sm-9 col-sm-offset-3">
			<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_13'] ?></h3>
			<br/>
			<form action='' method='post' id='Selected_Action'>
				<div class="row">
					<div class="col-sm-4">
						<label ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_14'] ?>:</label>
						<br/>
						<div class="form-group space-top-10">
							<select name='architecture' class="form-control width-auto">
							<?php
								foreach($architectures as $key => $value) {
								  if (!empty($value)){
										echo "<option value='".$value."'>".$value."</option>";
									}
								}
							?>
							</select>
						</div>
					</div>
					<div class="col-sm-6">
						<label><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_15'] ?>:</label>
						<div class="checkbox">
							<label><input type='checkbox' name='extralib' value='synopsys' /><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_16'] ?></label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-4 space-top-10">
						<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
						<button type="submit" name="post_action" value='Simulate_Project' class="btn btn-info full-row" id="Simulate_Project"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_13'] ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php if( !empty($vcd_files) ){ 
			include("pages/extra/waverforms_viewer.php");
		} ?>
<?php } ?>

<?php  if( $is_editor ){ ?>
<!-- Modal -->
	<div id="Download-Project-Modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			  <!-- Modal header menu-->
			<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_5'] ?></h3>
		  </div>
		  <div class="modal-body tab-content">
			  <!-- Modal Body Directory-->
			<form action='' method='post' id="download_project_form">
				<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_18'] ?></h4>
				<span class="row">
					<span class="col-sm-4">
						<label ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_19'] ?> : </label>
						<input type="checkbox" name="download_vhdl" id="download_vhdl" checked><br/>
					</span>
					<span class="col-sm-4">
						<label ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_20'] ?> : </label>
						<input type="checkbox" name="download_vcd"  id="download_vcd" checked><br/>
					</span>
					<span class="col-sm-4">
						<label ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_21'] ?> : </label>
						<input type="checkbox" name="download_log"  id="download_log" checked><br/>
						<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
						<input type="hidden" value="111" name="file_types"><br/>
					</span>
				</span>
				<span class="row">
					<span class="col-sm-12">
						<button type="submit" name="post_action" value='Download_Project' class="btn btn-info full-row" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_5'] ?></button>
					</span>
				</span>
			</form>
			
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_22'] ?></button>
		  </div>
		</div>
	  </div>
	</div>
<?php  } ?>

<?php if($user->validate_edit_rights($editors)){ ?>
	<!-- Modal -->
	<div id="Create-filedir-Modal" class="modal fade" role="dialog">
	  <div class="modal-dialog">
		<div class="modal-content">
		  <div class="modal-header">
			  <!-- Modal header menu-->
			<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_23'] ?></h3>
		  </div>
		  <div class="modal-body tab-content">
			<!-- Modal Body Create File-->
			<form action='' method='post'>
				<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_24'] ?></h4>
				<span class="row">
					<span class="col-sm-6">
						<input type="text" name="file_name" size='15' placeholder="File Name"/>
						<input type="hidden" value="<?php echo $_GET['project']; ?>" name="project_shortcode">
						<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
					</span>
					<span class="col-sm-6">
						<button type="submit" name="post_action" value='Create_file' class="btn btn-default"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_24'] ?></button>
					</span>
				</span>
			</form>
			<br/>
			<!-- Modal Body Upload File-->
			<form enctype="multipart/form-data" action="" method="post">
				<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_25'] ?></h4>
				<span class="row">
					<span class="col-sm-6">
						<input name="userfile" type="file" />
						<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
						<input type="hidden" name="upload_dir" value="<?php echo $current_dir; ?>" />
						<input type="hidden" value="<?php echo $_GET['project']; ?>" name="project_shortcode">
						<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
					</span>
					<span class="col-sm-6">
						<button type="submit" name="post_action" value='Upload_File' class="btn btn-default"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_25'] ?></button>
					</span>
				</span>
			</form>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_22'] ?></button>
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