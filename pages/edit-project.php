<?php 
	$project = $db->get_project_shortcode($_GET['short_code'], $_SESSION['vhdl_user']['id']);
	$editors = $db->get_project_editors($project['id']);

	if( $user->validate_ownership($editors) ){
?>
<div class="row">
	<div class="col-sm-6 col-sm-offset-3">
		<form action='' method='post' id='create-project'>
			<h4>Edit Project <?php echo $project['name']; ?></h4>
			<br />
			<label for="project_name">Project name:</label>
			<input type="text" name="project_name" size='25' class="form-control" placeholder="Project name" value="<?php echo $project['name']; ?>" required/><br />
			
			<label for="projet_authors_lookup">Add other People to the Project:</label>
			<input type="text" name="projet_authors_lookup" size='25' class="form-control typeahead" id="typeahead-input" autocomplete="off"/>
			<ul class="list-group" id="editor-users">
				<?php echo $gen->list_editors_li($editors); ?>
			</ul>
			<input type="hidden" name="projet_authors" id='projet_authors' value='<?php echo $gen->list_editors_comma($editors);  ?>' />
			
			<label for="project_share">Allow other people to view this project:</label><br />
			<label class="radio-inline"><input type="radio" name="project_share" value="1" <?php echo ($project['public']==1)? 'checked':''; ?>>Public</label>
			<label class="radio-inline"><input type="radio" name="project_share" value="0" <?php echo ($project['public']==0)? 'checked':''; ?>>Private</label>
			<br />
			<br />
			<label for="project_description">Project description :</label>
			<textarea form='create-project' name='project_description' class="form-control" rows="5" placeholder="Project description"><?php echo $project['description']; ?></textarea>
			<br />
			<input type="hidden" name="project_id" value='<?php echo $project['id']; ?>' />
			<button type="submit" name="post_action" value='edit_project' class="btn btn-default" >Edit Project</button>
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