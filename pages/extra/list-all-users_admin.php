<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
if( !isset($user->type) || $user->type != '1' ){
	header("location: ".$BASE_URL); 
	exit();
}
?>
<?php
	$found_users = $db->get_latest_users($name,$page,19);
	$found_user_num = $db->count_users($name);
	$alter=0;
?>

<div id="users_container">
	<ul class="list-group">
		<li class='list-group-item row'>
			<center>
				<span class="glyphicon glyphicon-plus-sign pointer strong" aria-hidden="true" data-toggle="modal" data-target="#Create-User-Modal"></span>
			</center>
		</li>
		<?php 
		foreach($found_users as $found_user){ 
			$link_user = $BASE_URL."/project/".$found_user['username']; 
			$link_edit =  $BASE_URL."/settings/".$found_user['id'];
			$alter = ($alter==1)?0:1;
		?>
			<li class="list-group-item row <?php echo ($alter==1)?"alternative_row":""; ?>">
				<a href="<?php echo $link_user; ?>" class="col-sm-4">
					<h3 class="list-group-item-heading inline-block">
						<?php echo $found_user['username']; ?>
					</h3>
				</a>
				<a href="<?php echo $link_edit; ?>" class="col-sm-offset-5 col-sm-1 btn btn-info">Edit</a>
				<form class="form" action="" method="post" onsubmit="confirm_user_removal()" >
					<input type="hidden" name="user_id" value="<?php echo $found_user['id']; ?>" />
					<button type="submit" class="col-sm-offset-1 col-sm-1 btn btn-danger" name="post_action" value="Remove_User_Admin">Remove</button>
				</form>
			</li>
		<?php } ?>
	</ul>

	<center>
		<ul class="pagination">
			<?php 
			if($found_user_num > 20){
				$j=1;
				for($i=0;$i<$found_user_num;$i+=20){
					echo "<li><a href='javascript:void(0)' onclick='admin_users_change_page($j);'>".$j."</a></li>";
					$j++;
				}
			}
			?>
		</ul>
	</center>

	<div id="Create-User-Modal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
			  <div class="modal-header">
				<h3>Create New User</h3>
			  </div>
			  <div class="modal-body tab-content">
				<form class="form" id="form_register" role="register" action="" method="post" data-toggle="validator" novalidate="false">
					<div class="form-group">
						<label for="email">Email address :</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Email address" data-error="The e-mail address you provided is invalid" required/>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<label for="username">Username :</label>
						<input type="text" class="form-control" name="username" id="username_reg" placeholder="Username" data-minlength="5" required/>
						<div class="help-block"></div>
					</div>
					<div class="form-group">
						<label for="active">Activated :</label>
						<select name="active">
							<option value="1" selected>Active</option>
							<option value="0">Inactive</option>
						</select>
						<div class="help-block"></div>
					</div>
					<div class="form-group">
						<label for="type">Type :</label>
						<select name="type">
							<option value="0" selected>User</option>
							<option value="1">Admin</option>
						</select>
						<div class="help-block">Minimum of 5 characters</div>
					</div>
					<div class="form-group">
						<label for="password">Password :</label>
						<input type="password" class="form-control" name="password" id="password_reg" placeholder="Password" data-minlength="5" required/>
						<div class="help-block">Minimum of 5 characters</div>
					</div>
					<div class="form-group">
						<label for="password_confirm">Confirm Password :</label>
						<input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Confirm Password" data-match="#password_reg" data-match-error="The passwords do not match" required/>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<label for="telephone">Mobile number (optional):</label>
						<input type="telephone" class="form-control" name="telephone" id="telephone" placeholder="Mobile number (optional)" data-error="The mobile number you provided is invalid" data-minlength="10" />
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<button type="submit" id="register_btn" class="btn btn-primary" name="post_action" value="Create_User_Admin">Create User</button>
					</div>
				</form>
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			  </div>
			</div>
		</div>
	</div>
</div>