<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
if( !isset($_SESSION['SID']) ){
	header("location: ".$BASE_URL); 
	exit();
}
?>
<?php 
	$file_name = $_GET['file'];	
	$path=$BASE.$_SESSION['SID']."/".$file_name;
	$file = $db->get_file_byname_sid($file_name,$_SESSION['SID']);
	echo "<a href='".$BASE_URL."'>".$_SESSION['SID']." ></a> ".$file_name;
?>
<br/><br/><br/>
<div class="row">
	<div class="col-sm-3 divider-vertical-right"> 
		Contents of : <?php echo $_SESSION['SID']; ?>/<?php echo $file_name; ?>
	</div>
	<div class="col-sm-9"> 
		SID projects are removed in server maintenance.<br/>
		SID Projects are always private.
	</div>
</div>

<div class="row">
	<div class="col-sm-12">
		<h3><?php echo $file_name; ?></h3>
		<?php if (file_exists($path)  && is_writable($path)){ ?>
			<div id='edit_status'></div>
			<div id="editor">
				<?php echo $gen->open_and_read_file($path); ?>
			</div>
			<script src="<?php echo $BASE_URL; ?>/src/ace.js" type="text/javascript" charset="utf-8"></script>
			<script>
				var editor = ace.edit("editor");
			</script>
				<div id="editor_buttons">
					<br/>
					<script type='text/javascript' charset='utf-8' src='<?php echo $BASE_URL; ?>/theme/js/ace-editor.js'></script>
					<input type='hidden' value='<?php echo $path; ?>' id='path' />
					<input type='hidden' value='<?php echo $file['id']; ?>' id='file_id' />
					<input type="hidden" value="SID" id="project_id">
					<input type='button' id='ace_save_button' name='save' value='Save Changes' class='btn btn-lg btn-info center-block'>
					<!-- //print_close_editor_window_button($file); -->
				</div>
				<script>
					editor.setTheme("ace/theme/monokai");
					editor.getSession().setMode("ace/mode/vhdl");
					editor.setOption("showPrintMargin", false);
				</script>
		<?php }else{ ?>
			<div class="alert alert-danger">
				Can not edit file. Make sure the file exists and has the right permissions.
			</div>
		<?php } ?>
		<br />
	</div>
</div>