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
	if( $user->validate_edit_rights($editors) || $project['public']==1){
		$_GET['dir'] = $gen->clear_dir($_GET['dir']);
		$files = $db->get_project_files($project['id'], $_GET['dir']);
		
		if($_SESSION['vhdl_user']['username'] == "Guest"){
			$current_dir=$BASE.$_SESSION['PID']."/"; 
		}else{
			if($_GET['dir']=='/'){
				$current_dir=$BASE_DIR.$search_user['username']."/".$project['short_code']."/"; 
			}else{
				$current_dir=$BASE_DIR.$search_user['username']."/".$project['short_code'].$_GET['dir']."/"; 
			}
		}
		
		$vcd_files = glob($current_dir."*.vcd");
		
		//we want here to display all valid architectures
		$command="cd ".$current_dir.";/usr/lib/ghdl/bin/ghdl -d | grep architecture | awk ' {print $4,$2} '";
		$shell_output=shell_exec($command);
		//Extract the lines
		$architectures = explode(PHP_EOL, $shell_output);
		
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
			<div class="col-sm-12 space-top-10">
				<form action='' method='post' id='Selected_Action'>
					<input type="hidden" value="" name="selected_ids" id="selected_ids" />
					<input type="hidden" value="<?php echo $search_user['username']; ?>" name="owner">
					<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
					<button type="submit" name="post_action" value='Post_Library_Selected' class="btn btn-info" >Post Selected as Library</button>
					<button type="submit" name="post_action" value='Compile_Selected' class="btn btn-success" >Compile Selected</button>
					<button type="submit" name="post_action" value='Remove_Selected' class="btn btn-danger" >Remove Selected</button>
				</form>
			</div>
		</div>
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-9 col-sm-offset-3">
		<h3>Simulate Project</h3>
		<br/>
		<form action='' method='post' id='Selected_Action'>
			<div class="row">
				<div class="col-sm-4">
					<label >Select Architecture:</label>
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
					<label>Additional Option:</label>
					<div class="checkbox">
						<label><input type='checkbox' name='extralib' value='synopsys' />Include synopsis library for more primary units.</label>
					</div>
					<div class="checkbox">
						<label><input type='checkbox' name='extrasim' value='vcd' checked/>Create a value changed dump VCD wave trace file.</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-sm-4 space-top-10">
					<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id">
					<button type="submit" name="post_action" value='Simulate_Project' class="btn btn-info full-row" id="Simulate_Project">Simulate Project</button>
				</div>
			</div>
		</form>
	</div>
	
	<?php if( !empty($vcd_files) ){ ?>
	<div class="col-sm-9 col-sm-offset-3">
		<h3>Display Waveforms</h3>
		<br/>
		<div class="row">
			<div class="col-sm-4">
				<label >Select VCD file:</label>
				<br/>
				<div class="form-group space-top-10">
					<select name="vcd_file" id="vcd_file" class="form-control width-auto">
					<?php
						foreach($vcd_files as $vcd) {
							$vcd_name = end(explode('/',$vcd));
							echo "<option value='".$vcd_name."'>".$vcd_name."</option>";
						}
					?>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-4 space-top-10">
				<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id" id="project_id">
				<button type="submit" class="btn btn-info full-row" id="Display_Waveform">Display Waveform</button>
			</div>
		</div>
	</div>
	<div id="waveforms_container" class="hidden">
		<div class="row">
			<div id="signals" class="col-sm-2">
				aaaa
			</div>
			<div id="canvas_div" class="col-sm-8">
				aaaa
				<canvas id="WavesCanvas"width="800" height="1" class="pointer">
					Your browser does not support the HTML5 canvas tag.</canvas>
				<canvas id="BufferCanvas">
					Your browser does not support the HTML5 canvas tag.</canvas>
				<div id="WaveImage"></div>
				Time Scale (default 100%) : <div class="slider_main" id="slider_main"><div class="slider_button" id="slider_button"></div><input type="hidden" id="simulation_zoom" value="100"/></div><div class="slider_info" id="slider_info">100%</div>
			</div>
		</div>
	</div>
	<script src="<?php echo $BASE_URL; ?>/theme/js/waveforms_viewer.js" type="text/javascript" charset="utf-8"></script>
	<?php } ?>
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
						<input type="hidden" name="upload_dir" value="<?php echo $current_dir; ?>" />
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