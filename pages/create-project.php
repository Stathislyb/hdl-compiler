<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php
if($user->id > 0){
?>
<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<form action='' method='post' id='create-project' data-toggle="validator">
			<h2><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_1'] ?></h2>
			<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_2'] ?>:</label>
			<div class="form-group">
				<input type="text" name="project_name" size='25' class="form-control" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_2'] ?>" data-minlength="5" required/>
				<div class="help-block"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?></div>
			</div>
			<label for="projet_authors_lookup"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_3'] ?>:</label>
			<input type="text" name="projet_authors_lookup" size='25' class="form-control add-editors-typeahead" id="typeahead-input" autocomplete="off"/>
			<ul class="list-group" id="editor-users">
			</ul>
			<input type="hidden" name="projet_authors" id='projet_authors' value='' />
			
			<label for="project_share"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_4'] ?>:</label><br />
			<label class="radio-inline"><input type="radio" name="project_share" value="1" checked><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_5'] ?></label>
			<label class="radio-inline"><input type="radio" name="project_share" value="0"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_6'] ?></label>
			<br />
			<br />
			<label for="project_description"><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_7'] ?>:</label>
			<textarea form='create-project' name='project_description' class="form-control" rows="5" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_7'] ?>"></textarea>
			<br />
			<?php echo (isset($_GET['user']) && !empty($_GET['user']) )? "<input type='hidden' name='original_user' value='".$_GET['user']."' />":''; ?>
			<button type="submit" name="post_action" value='create_project' class="btn btn-default" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_8'] ?></button>
		</form>
	</div>
</div>
<?php
}else{
?>
<div class="row">
	<div class="col-sm-4 col-md-offset-4">
		<?php echo $messages->text[$_SESSION['vhdl_lang']]['create_project_9'] ?>
	</div>
</div>

<?php	
}
?>