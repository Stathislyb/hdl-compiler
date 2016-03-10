<?php 
	
	$gen = new General;
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['project'], $search_user['id']);
	$editors = $db->get_project_editors($project['id']);
	$path = $BASE_DIR.$search_user['username']."/".$_GET['project']."/".$_GET['file'];

	if($project['public']==1){
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
			<h3><?php echo $file_name; ?></h3>
			<?php if (file_exists($path)  && is_writable($path)){ ?>
				<div id='edit_status'></div>
				<div id="editor">
					<?php echo $gen->open_and_read_file($path); ?>
				</div>
				<script src="/src/ace.js" type="text/javascript" charset="utf-8"></script>
				<script>
					var editor = ace.edit("editor");
				</script>
				<?php if( $user->validate_edit_rights($editors) ){ ?>
					<div id="editor_buttons">
						<br/>
						<script type='text/javascript' charset='utf-8' src='<?php echo $BASE_URL; ?>/theme/js/ace-editor.js'></script>
						<input type='hidden' value='<?php echo $path; ?>' id='path' />
						<input type='button' id='ace_save_button' name='save' value='Save Changes' class='btn btn-lg btn-info center-block'>
						<!-- //print_close_editor_window_button($file); -->
					</div>
					<script>
						editor.setTheme("ace/theme/monokai");
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
				<?php } 
			}else{ ?>
				<div class="alert alert-danger">
					Can not edit file. Make sure the file exists and has the right permissions.
				</div>
			<?php } ?>
			<br />
		</div>
	
<br/>
<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>