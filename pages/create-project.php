<?php
if($user->id > 0){
?>
<tr>
	<td>
		Step1
	</td>
	<td>
		<div class="topic1" id="pid">
			<form action='' method='post' id='create-project'>
				Project name :<br /><input type="text" name="project_name" size='25' /><br />
				Add other People to the Project :<br /><input type="text" name="projet_authors" size='25' /><br />
				Project description:<br />
				<textarea form='create-project' name='project_description'></textarea>
				<br />
				<button type="submit" name="post_action" value='create_project'>Create Project</button>
			</form>
		</div>
	</td>
</tr>
<?php
}else{
?>
<tr>
	<td>
		Step1
	</td>
	<td>
		<div class="topic1" id="pid">
			You need to log in to access this feature.
		</div>
	</td>
</tr>
<?php	
}
?>