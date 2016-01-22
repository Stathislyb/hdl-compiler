<?php 
	$project = $db->get_project_shortcode($_GET['short_code'], $_SESSION['vhdl_user']['id']);
	$editors = $db->get_project_editors($project['id']);
	
	function list_editors($editors){
		$list="";
		foreach($editors as $editor){
			$list.= $editor['username'].",";
		}
		echo rtrim($list, ",");
	}

	if( $user->validate_ownership($editors) ){
?>
<tr>
	<td>
		Step1
	</td>
	<td>
		<div class="topic1" id="pid">
			<form action='' method='post' id='edit-project'>
				Project name :<br /><input type="text" name="project_name" size='25' value='<?php echo $project['name']; ?>' /><br />
				Add other People to the Project :<br /><input type="text" name="projet_authors" size='25' value='<?php list_editors($editors); ?>' /><br />
				Allow other people to view this project : <br />
				<select name="project_share">
					<option value="1" <?php echo ($project['public']==1)? 'selected':''; ?>>Public</option>
					<option value="0" <?php echo ($project['public']==0)? 'selected':''; ?>>Private</option>
				</select><br />
				Project description:<br />
				<textarea form='edit-project' name='project_description'><?php echo $project['description']; ?></textarea>
				<br />
				<input type="hidden" name="project_id" value='<?php echo $project['id']; ?>' />
				<button type="submit" name="post_action" value='edit_project'>Edit Project</button>
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
			You do not have ownership over this project.
		</div>
	</td>
</tr>
<?php
	}											  
?>