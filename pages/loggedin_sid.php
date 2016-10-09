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
		<?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_2'] ?> : <?php echo $_SESSION['SID']; ?>/
	</div>
	<div class="col-sm-9"> 
		<?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_3'] ?><br/>
		<?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_4'] ?>
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
					<button type="submit" name="post_action" value='SID_Compile_Selected' class="btn btn-success" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_11'] ?></button>
					<button type="submit" name="post_action" value='SID_Remove_Selected' class="btn btn-danger" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_12'] ?></button>
				</form>
			</div>
		</div>
	</div>
</div>
<hr/>
<?php if($is_compiled){ ?>
	<div class="row">
		<div class="col-sm-9">
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
						<div class="checkbox">
							<label><input type='checkbox' name='extrasim' value='vcd' checked/><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_17'] ?></label>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="form-group col-sm-4 space-top-10">
						<button type="submit" name="post_action" value='SID_Simulate_Project' class="btn btn-info full-row" id="Simulate_Project"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_13'] ?></button>
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
		<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_23'] ?></h3>
	  </div>
	  <div class="modal-body tab-content">
		<!-- Modal Body Create File-->
		<form action='' method='post'>
			<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_24'] ?></h4>
			<span class="row">
				<span class="col-sm-6">
					<input type="text" name="file_name" size='15' placeholder="File Name"/>
				</span>
				<span class="col-sm-6">
					<button type="submit" name="post_action" value='SID_Create_file' class="btn btn-default"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_24'] ?></button>
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
				</span>
				<span class="col-sm-6">
					<button type="submit" name="post_action" value='SID_Upload_File' class="btn btn-default"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_25'] ?></button>
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
