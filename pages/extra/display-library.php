<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 
	
	$library = $db->get_library($_GET['short_code'], $user->type);
	if( empty($library) ){
		header("Location:".$BASE_URL."/libraries");
		exit();
	}
	$search_user = $db->get_user_information($library['owner_id'],"id");
	$path = $BASE_DIR."libraries/".$library['name'];
	$user_projects = $db->get_user_projects($_SESSION['vhdl_user']['id']);
?>

<div class="row">
	<div class="col-sm-3 divider-vertical-right"> 
		<?php echo $library['name']."<br/> ".$messages->text[$_SESSION['vhdl_lang']]['project_dir_1']." ".$search_user['username']; ?>
	</div>
	<div class="col-sm-9"> 
		<a href='<?php echo $BASE_URL; ?>/libraries' class="btn btn-primary btn-outline pull-right" role="button"><?php echo $messages->text[$_SESSION['vhdl_lang']]['display_lib_1'] ?></a>
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
			<input type="hidden" value="<?php echo $library['id']; ?>" name="library_id"/>
			<button type="submit" name="post_action" value='Import_Library' class="btn btn-success" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['display_lib_2'] ?></button>
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
				<script src="<?php echo $BASE_URL; ?>/src/ace.js" type="text/javascript" charset="utf-8"></script>
				<script>
					var editor = ace.edit("editor");
				</script>
				<?php if( $user->type==1 ){ ?>
					<div id="editor_buttons">
						<br/>
						<script type='text/javascript' charset='utf-8' src='<?php echo $BASE_URL; ?>/theme/js/ace-editor.js'></script>
						<input type='hidden' value='<?php echo $path; ?>' id='path' />
						<input type='hidden' value='<?php echo $library['id']; ?>' id='library_id' />
						<input type='button' id='ace_save_button_library' name='save' value='<?php echo $messages->text[$_SESSION['vhdl_lang']]['ace_save'] ?>' class='btn btn-lg btn-info center-block'>
						<br/>
						<?php if($library['approved']==0){ ?>
							<form class="form" action="" method="post" >
								<input type="hidden" name="library_id" value="<?php echo $library['id']; ?>" />
								<button type="submit" class="btn btn-lg btn-success center-block" name="post_action" value="Approve_Component_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_2'] ?></button>
							</form>
						<?php }else{ ?>
							<form class="form" action="" method="post" >
								<input type="hidden" name="library_id" value="<?php echo $library['id']; ?>" />
								<button type="submit" class="btn btn-lg btn btn-warning center-block" name="post_action" value="Dispprove_Component_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_3'] ?></button>
							</form>
						<?php } ?>
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
			<?php }else{ ?>
				<div class="alert alert-danger">
					<?php echo $messages->text[$_SESSION['vhdl_lang']]['ace_error'] ?>
				</div>
			<?php } ?>
			<br />
		</div>
	
<br/>
