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
<h2><?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_1'] ?> [<b><?php echo $user_info['username']; ?></b>]</h2>
<hr/>
<form class="form" id="form_edit_user" role="edit_user" action="" method="post" data-toggle="validator" novalidate="false">
	<div class="row">
		<div class="col-sm-6"> 
			<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_1'] ?>:</label>
			<div class="form-group">
				<input type="text" name="email_edit" size='25' class="form-control" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?>" data-minlength="5" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_1'] ?>" value="<?php echo $user_info['email']; ?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="col-sm-6"> 
			<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_2'] ?>:</label>
			<div class="form-group">
				<input type="password" name="new_password_edit" id="new_password_edit" size='25' autocomplete="off" class="form-control" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?>" data-minlength="5" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_2'] ?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6"> 
			<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_5'] ?>:</label>
			<div class="form-group">
				<input type="text" name="phone_edit" size='25' class="form-control" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_4'] ?>" data-minlength="10" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_5'] ?>" value="<?php echo $user_info['telephone']==0?'':$user_info['telephone']; ?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
		<div class="col-sm-6"> 
			<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_3'] ?>:</label>
			<div class="form-group">
				<input type="password" name="rep_password_edit" size='25' class="form-control" autocomplete="off" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_3'] ?>" data-match="#new_password_edit" data-minlength="5" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_3'] ?>" />
				<div class="help-block with-errors"></div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6"> 
			<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_4'] ?>:</label>
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
				<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_5'] ?>:</label>
				<div class="form-group">
					<input type="text" name="available_space_edit" size='5' class="form-control" autocomplete="off" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_7'] ?>" data-minlength="1" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_5'] ?>" value="<?php echo $user_info['available_space']; ?>" />
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
				<button type="submit" id="edit_user_btn" class="btn btn-primary" name="post_action" value="Edit_User_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_6'] ?></button>
			<?php }else{	?>
				<label for="project_name"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_3'] ?>:</label>
				<div class="form-group">
					<input type="password" name="password_edit" size='25' autocomplete="off" class="form-control" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?>" data-minlength="5" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_3'] ?>" required/>
					<div class="help-block with-errors"></div>
				</div>
				<button type="submit" id="edit_user_btn" class="btn btn-primary" name="post_action" value="Edit_User"><?php echo $messages->text[$_SESSION['vhdl_lang']]['account_settings_6'] ?></button>
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