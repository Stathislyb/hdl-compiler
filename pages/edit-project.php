<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	$project = $db->get_project_shortcode($_GET['short_code'], $search_user['id']);
	$editors = $db->get_project_editors($project['id']);

	if( $user->validate_ownership($editors) ){
?>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3">
		<form action='' method='post' id='create-project'  data-toggle="validator">
			<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['edit_project_1'] ?> <?php echo $project['name']; ?></h4>
			<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_2'] ?>:</label>
			<div class="form-group">
				<input type="text" name="project_name" size='25' class="form-control" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?>" data-minlength="5" placeholder="Project name" value="<?php echo $project['name']; ?>" required/>
				<div class="help-block with-errors"></div>
			</div>
			<label for="projet_authors_lookup"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_3'] ?>:</label>
			<input type="text" name="projet_authors_lookup" size='25' class="form-control add-editors-typeahead" id="typeahead-input" autocomplete="off"/>
			<ul class="list-group" id="editor-users">
				<?php echo $gen->list_editors_li($editors); ?>
			</ul>
			<input type="hidden" name="projet_authors" id='projet_authors' value='<?php echo $gen->list_editors_comma($editors);  ?>' />
			
			<label for="project_share"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_4'] ?>:</label><br />
			<label class="radio-inline"><input type="radio" name="project_share" value="1" <?php echo ($project['public']==1)? 'checked':''; ?>><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_5'] ?></label>
			<label class="radio-inline"><input type="radio" name="project_share" value="0" <?php echo ($project['public']==0)? 'checked':''; ?>><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_6'] ?></label>
			<br />
			<br />
			<label for="project_description"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_7'] ?> :</label>
			<textarea form='create-project' name='project_description' class="form-control" rows="5" placeholder="Project description"><?php echo $project['description']; ?></textarea>
			<br />
			<input type="hidden" name="project_id" value='<?php echo $project['id']; ?>' />
			<button type="submit" name="post_action" value='edit_project' class="btn btn-default" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['edit_project_1'] ?></button>
		</form>
	</div>
</div>

<?php
	}else{
?>
<div class="row">
	<div class="col-sm-4 col-md-offset-4">
		You need to log in to access this feature.
	</div>
</div>

<?php
	}											  
?>