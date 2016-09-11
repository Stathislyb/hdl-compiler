<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 
if( isset($_SESSION['vhdl_user']['id']) ){
	if( $_SESSION['vhdl_user']['id']>0){
		$edit_user_id = $user->id;
		if( isset($_GET['user_id']) && $_GET['user_id']>0 &&  $user->type==1 ){
			$edit_user_id=$_GET['user_id'];
		}
		$user_info = $db->get_user_information($edit_user_id,"id");
		$ace_themes = array("Chrome","Clouds","Clouds Midnight","Cobalt","Crimson Editor","Dawn","Eclipse","Idle Fingers",
				"Kr Theme","Merbivore","Merbivore Soft","Mono Industrial","Monokai","Pastel On Dark","Solarized Dark",
				"Solarized Light","TextMate","Tomorrow","Tomorrow Night","Tomorrow Night Blue","Tomorrow Night Bright",
				"Tomorrow Night Eighties","Twilight","Vibrant Ink");
?>
<br/><br/><br/>
<h2>Account Settings [<b><?php echo $user_info['username']; ?></b>]</h2>
<hr/>
<form class="form" id="form_edit_user" role="edit_user" action="" method="post" data-toggle="validator" novalidate="false">
	<div class="row">
		<div class="col-sm-6"> 
			<label for="project_name">E-mail:</label>
			<div class="form-group">
				<input type="text" name="email_edit" size='25' class="form-control" data-error="Minimum of 5 characters" data-minlength="5" placeholder="E-mail" value="<?php echo $user_info['email']; ?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="col-sm-6"> 
			<label for="project_name">New Password:</label>
			<div class="form-group">
				<input type="password" name="new_password_edit" id="new_password_edit" size='25' autocomplete="off" class="form-control" data-error="Minimum of 5 characters" data-minlength="5" placeholder="New Password" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6"> 
			<label for="project_name">Phone number:</label>
			<div class="form-group">
				<input type="text" name="phone_edit" size='25' class="form-control" data-error="The mobile number you provided is invalid" data-minlength="10" placeholder="Phone number" value="<?php echo $user_info['telephone']==0?'':$user_info['telephone']; ?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="col-sm-6"> 
			<label for="project_name">Repeat new Password:</label>
			<div class="form-group">
				<input type="password" name="rep_password_edit" size='25' class="form-control" autocomplete="off" data-error="Minimum of 5 characters" data-match="#new_password_edit" data-minlength="5" placeholder="Repeat new Password" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6"> 
			<label for="project_name">Editor Theme:</label>
			<div class="form-group">
				<select name="ace_theme" id="ace_theme" class="form-control">
					<?php
						foreach ($ace_themes as $key => $theme) {
							$selected = ($key==$user_info['theme'])?"selected":"";
							echo "<option value='".$key."' ".$selected.">".$theme."</option>";
						}
					?>
				</select>
			</div>
		</div>
		<?php if( $user->type==1 ){	?>
			<div class="col-sm-6"> 
				<label for="project_name">Available Disk Space (MB) :</label>
				<div class="form-group">
					<input type="text" name="available_space_edit" size='5' class="form-control" autocomplete="off" data-error="Minimum of 1 characters" data-minlength="1" placeholder="Available Space in MB" value="<?php echo $user_info['available_space']; ?>" />
					<div class="help-block with-errors"></div>
				</div>
			</div>
		<?php }	?>
	</div>
	<hr/>
	<div class="row">
		<div class="col-sm-6"> 
		
			<?php if( $user->type==1 ){	?>
				<input type="hidden" name="user_id_edit" value="<?php echo $edit_user_id; ?>" />
				<button type="submit" id="edit_user_btn" class="btn btn-primary" name="post_action" value="Edit_User_Admin">Submit</button>
			<?php }else{	?>
				<label for="project_name">Password:</label>
				<div class="form-group">
					<input type="password" name="password_edit" size='25' autocomplete="off" class="form-control" data-error="Minimum of 5 characters" data-minlength="5" placeholder="Password" required/>
					<div class="help-block with-errors"></div>
				</div>
				<button type="submit" id="edit_user_btn" class="btn btn-primary" name="post_action" value="Edit_User">Submit</button>
			<?php }	?>
		</div>
	</div>
	<hr/>
</form>
<?php
	}else{
		header('location:'.$BASE_URL);
	}	
}else{
	header('location:'.$BASE_URL);
}		
?>