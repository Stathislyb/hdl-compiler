<?php
if($user->id > 0){
?>
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<form action='' method='post' id='create-project' data-toggle="validator">
			<h2>Create New Project</h2>
			<label for="project_name">Project name:</label>
			<div class="form-group">
				<input type="text" name="project_name" size='25' class="form-control" placeholder="Project name" data-minlength="5" required/>
				<div class="help-block">Minimum of 5 characters</div>
			</div>
			<label for="projet_authors_lookup">Add other People to the Project:</label>
			<input type="text" name="projet_authors_lookup" size='25' class="form-control typeahead" id="typeahead-input" autocomplete="off"/>
			<ul class="list-group" id="editor-users">
			</ul>
			<input type="hidden" name="projet_authors" id='projet_authors' value='' />
			
			<label for="project_share">Allow other people to view this project:</label><br />
			<label class="radio-inline"><input type="radio" name="project_share" value="1" checked>Public</label>
			<label class="radio-inline"><input type="radio" name="project_share" value="0">Private</label>
			<br />
			<br />
			<label for="project_description">Project description:</label>
			<textarea form='create-project' name='project_description' class="form-control" rows="5" placeholder="Project description"></textarea>
			<br />
			<button type="submit" name="post_action" value='create_project' class="btn btn-default" >Create Project</button>
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