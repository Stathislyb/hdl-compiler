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
	$path = $BASE_DIR.$search_user['username']."/".$_GET['project']."/".$_GET['file'];
	$file = $db->get_file_byname($_GET['file'],$project['id']);
	$ace_themes = array("Chrome","Clouds","Clouds Midnight","Cobalt","Crimson Editor","Dawn","Eclipse","Idle Fingers",
				"Kr Theme","Merbivore","Merbivore Soft","Mono Industrial","Monokai","Pastel On Dark","Solarized Dark",
				"Solarized Light","TextMate","Tomorrow","Tomorrow Night","Tomorrow Night Blue","Tomorrow Night Bright",
				"Tomorrow Night Eighties","Twilight","Vibrant Ink");
	if(isset($_SESSION['vhdl_user']) && $_SESSION['vhdl_user']['id']>0){
		$user_theme_id = $db->get_user_theme($_SESSION['vhdl_user']['id']);
	}else{
		$user_theme_id = 0;
	}
	$user_theme = strtolower(str_replace(' ', '_',$ace_themes[$user_theme_id]));
	
	if($project['public']==1 && !empty($_GET['file'])){
		$file_name = substr(strrchr($_GET['file'], '/'), 1 );
		if(empty($file_name)){
			$file_name = $_GET['file'];
			$dir = $gen->clear_dir("/");
		}else{
			$dir_length = strlen($_GET['file']) - strlen($file_name)-1;
			$dir = substr($_GET['file'],0,$dir_length );
			$dir = $gen->clear_dir($dir);
		}
		
		echo $gen->path_to_links($dir, $search_user['username'], $project['name'], $BASE_URL);
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

<div class="row">
	<div class="col-sm-12">
		<div class="row">
			<div class="col-sm-10">
				<h3><?php echo $file_name; ?></h3>
			</div>
			<?php if( isset($user) ){ ?>
			<div class="col-sm-2">
				<div class="space-top-20">
					<select id="ace_theme" class="form-control">
						<?php
							foreach ($ace_themes as $key => $theme) {
								$selected = ($key==$user_theme_id)?"selected":"";
								echo "<option value='".$key."' ".$selected.">".$theme."</option>";
							}
						?>
					</select>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php if (file_exists($path)  && is_writable($path)){ ?>
			<div id='edit_status'></div>
			<div id="editor">
				<?php echo $gen->open_and_read_file($path); ?>
			</div>
			<script src="<?php echo $BASE_URL; ?>/src/ace.js" type="text/javascript" charset="utf-8"></script>
			<script>
				var editor = ace.edit("editor");
			</script>
			<?php if( isset($user) ){ ?>
				<?php if( $user->validate_edit_rights($editors) ){ ?>
					<div id="editor_buttons">
						<br/>
						<script type='text/javascript' charset='utf-8' src='<?php echo $BASE_URL; ?>/theme/js/ace-editor.js'></script>
						<input type='hidden' value='<?php echo $path; ?>' id='path' />
						<input type='hidden' value='<?php echo $file['id']; ?>' id='file_id' />
						<input type='button' id='ace_save_button' name='save' value='Save Changes' class='btn btn-lg btn-info center-block'>
						<!-- //print_close_editor_window_button($file); -->
					</div>
					<script>
						editor.setTheme("ace/theme/<?php echo $user_theme; ?>");
						editor.getSession().setMode("ace/mode/vhdl");
						editor.setOption("showPrintMargin", false);
					</script>
				<?php }else{ ?>
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
				<?php } ?>
			<?php } ?>
		<?php }else{ ?>
			<div class="alert alert-danger">
				Can not edit file. Make sure the file exists and has the right permissions.
			</div>
		<?php } ?>
		<br />
	</div>
	
</div>
<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>