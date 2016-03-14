<?php 
	
	$library = $db->get_library($_GET['short_code']);
	$search_user = $db->get_user_information($library['owner_id'],"id");
	$path = $BASE_DIR."libraries/".$library['name'];
	$user_projects = $db->get_user_projects($_SESSION['vhdl_user']['id']);
?>

<div class="row">
	<div class="col-sm-3 divider-vertical-right"> 
		<?php echo $library['name']."<br/> Created by ".$search_user['username']; ?>
	</div>
	<div class="col-sm-9"> 
		<a href='/libraries' class="btn btn-primary btn-outline pull-right" role="button">View All Libraries </a>
		<form action='' method='post'>
			<select name="project_id" class="form-control input-width-fix">
				<?php 
				foreach ($user_projects as $project) {
					echo "<option value='".$project['id']."'>".$project['name']."</option>";
				}
				if( empty($user_projects) ){
					echo "<option value='0'>You have no projects.</option>";
				}
				?>
			</select>
			<input type="hidden" value="<?php echo $library['name']; ?>" name="library"/>
			<button type="submit" name="post_action" value='Import_Library' class="btn btn-success" >Include to Project</button>
		</form>
	</div>
</div>

<div class="row">
		<div class="col-sm-12">
			<h3><?php echo $library['name']; ?></h3>
			<?php if (file_exists($path)  && is_writable($path)){ ?>
				<div id='edit_status'></div>
				<div id="editor">
					<?php echo $gen->open_and_read_file($path); ?>
				</div>
				<script src="/src/ace.js" type="text/javascript" charset="utf-8"></script>
				<script>
					var editor = ace.edit("editor");
				</script>
				<script>
					editor.setOptions({
						readOnly: true,
						highlightActiveLine: false,
						highlightGutterLine: false
					});
					editor.renderer.$cursorLayer.element.style.opacity=0;
					editor.textInput.getElement().disabled=true;
					editor.commands.commmandKeyBinding={};
					editor.setOption("showPrintMargin", false);
				</script>
			<?php }else{ ?>
				<div class="alert alert-danger">
					Can not edit file. Make sure the file exists and has the right permissions.
				</div>
			<?php } ?>
			<br />
		</div>
	
<br/>
