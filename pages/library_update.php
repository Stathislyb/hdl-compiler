<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 
	if($user->type != 1){
		header("Location:".$BASE_URL."/libraries");
		exit();
	}
	$library = $db->get_library($_GET['short_code'], $user->type);
	if( empty($library) ){
		header("Location:".$BASE_URL."/libraries");
		exit();
	}
	$search_user = $db->get_user_information($library['owner_id'],"id");
	$path_original = $BASE_DIR."libraries/".$library['name'];
	$path_suggestion = $BASE_DIR."update_libraries/".$library['name'];
	$user_projects = $db->get_user_projects($_SESSION['vhdl_user']['id']);
?>

<div class="row">
	<div class="col-sm-3 divider-vertical-right"> 
		<?php echo $library['name']."<br/> Created by ".$search_user['username']; ?>
	</div>
	<div class="col-sm-9"> 
		<a href='<?php echo $BASE_URL; ?>/libraries' class="btn btn-primary btn-outline pull-right" role="button">View All Libraries </a>
	</div>
</div>

<div class="row">
		<div class="col-sm-12">
			<h3><?php echo $library['name']; ?></h3>
			<div class="row">
				<div class="col-sm-6">
					<h4>Original :</h4>
					<script src="<?php echo $BASE_URL; ?>/src/ace.js" type="text/javascript" charset="utf-8"></script>
					<?php if (file_exists($path_original)  && is_writable($path_original)){ ?>
						<div id="editor">
							<?php echo $gen->open_and_read_file($path_original); ?>
						</div>
						<script>
							var editor = ace.edit("editor");
							editor.setOptions({
								readOnly: true,
								highlightActiveLine: false,
								highlightGutterLine: false,
								maxLines: 36
							});
							editor.renderer.$cursorLayer.element.style.opacity=0;
							editor.textInput.getElement().disabled=true;
							editor.setOption("showPrintMargin", false);
							$(editor).resize(); 
						</script>
					<?php }else{ ?>
						<div class="alert alert-danger">
							Can not edit file. Make sure the file exists and has the right permissions.
						</div>
					<?php } ?>
				</div>
				<div class="col-sm-6">
					<h4>Suggested Update :</h4>
					<?php if (file_exists($path_suggestion)  && is_writable($path_suggestion)){ ?>
						<div id="editor2">
							<?php echo $gen->open_and_read_file($path_suggestion); ?>
						</div>
						<script src="<?php echo $BASE_URL; ?>/src/ace.js" type="text/javascript" charset="utf-8"></script>
						<script>
							var editor2 = ace.edit("editor2");
							editor2.setOptions({
								readOnly: true,
								highlightActiveLine: false,
								highlightGutterLine: false,
								maxLines: 36
							});
							editor2.renderer.$cursorLayer.element.style.opacity=0;
							editor2.textInput.getElement().disabled=true;
							editor2.setOption("showPrintMargin", false);
							editor2.resize(); 
						</script>
					<?php }else{ ?>
						<div class="alert alert-danger">
							Can not edit file. Make sure the file exists and has the right permissions.
						</div>
					<?php } ?>
				</div>
			</div>
			<br/>
			<div class="row">
				<form class="form" action="" method="post" >
					<input type="hidden" name="library_id" value="<?php echo $library['id']; ?>" />
					<button type="submit" class="btn btn-lg btn-success center-block" name="post_action" value="Apply_Update_Admin">Apply Update</button>
					<br/>
					<button type="submit" class="btn btn-lg btn-danger center-block" name="post_action" value="Discard_Update_Admin">Discard Update</button>
				</form>
			</div>
			<br />
		</div>
	
<br/>
