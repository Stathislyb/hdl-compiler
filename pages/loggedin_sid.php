<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 

	$files =$db->get_sid_files($_SESSION['SID']);
	$current_dir=$BASE.$_SESSION['SID']."/";
	$vcd_files = glob($current_dir."*.vcd");
	//we want here to display all valid architectures
	$command="cd ".$current_dir.";/usr/lib/ghdl/bin/ghdl -d | grep architecture | awk ' {print $4,$2} '";
	$shell_output=shell_exec($command);
	//Extract the lines
	$architectures = explode(PHP_EOL, $shell_output);
	$is_compiled = false;
?>
<br/><br/><br/>
<div class="row">
	<div class="col-sm-3 divider-vertical-right"> 
		Contents of : <?php echo $_SESSION['SID']; ?>/
	</div>
	<div class="col-sm-9"> 
		SID projects are removed in server maintenance.<br/>
		SID Projects are always private.
	</div>
</div>
<hr/>
<div class="row">
	<div class="col-sm-9">
		<ul class="list-group no-shadow">
			<li class='list-group-item no-border'>
				<span class='pull-right'><input type="checkbox" id="select_all"></span>
			</li>
		</ul>
		<?php require('pages/extra/list-dirfiles-sid.php'); ?>
		<div class="row">
			<div class="col-sm-12 space-top-10">
				<form action='' method='post' id='Selected_Action'>
					<input type="hidden" value="" name="selected_ids" id="selected_ids" />
					<input type="hidden" value="<?php echo $_SESSION['SID'] ?>" name="sid">
					<button type="submit" name="post_action" value='SID_Compile_Selected' class="btn btn-success" >Compile Selected</button>
					<button type="submit" name="post_action" value='SID_Remove_Selected' class="btn btn-danger" >Remove Selected</button>
				</form>
			</div>
		</div>
	</div>
</div>
<hr/>
<?php if($is_compiled){ ?>
	<div class="row">
		<div class="col-sm-9">
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
						<button type="submit" name="post_action" value='SID_Simulate_Project' class="btn btn-info full-row" id="Simulate_Project">Simulate Project</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<?php if( !empty($vcd_files) ){ 
			include("pages/extra/waverforms_viewer.php");
		} ?>
<?php } ?>
<!-- Modal -->
<div id="Create-filedir-Modal" class="modal fade" role="dialog">
  <div class="modal-dialog">
	<div class="modal-content">
	  <div class="modal-header">
		  <!-- Modal header menu-->
		<h3>Add File</h3>
	  </div>
	  <div class="modal-body tab-content">
		<!-- Modal Body Create File-->
		<form action='' method='post'>
			<h4>Create File</h4>
			<span class="row">
				<span class="col-sm-6">
					<input type="text" name="file_name" size='15' placeholder="File Name"/>
				</span>
				<span class="col-sm-6">
					<button type="submit" name="post_action" value='SID_Create_file' class="btn btn-default">Create File</button>
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
					<input type="hidden" name="MAX_FILE_SIZE" value="5242880" />
				</span>
				<span class="col-sm-6">
					<button type="submit" name="post_action" value='SID_Upload_File' class="btn btn-default">Upload File</button>
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
