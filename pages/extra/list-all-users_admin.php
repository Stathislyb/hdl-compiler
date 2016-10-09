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
	$found_users = $db->get_latest_users_admin($name,$page,19);
	$found_user_num = $db->count_users($name);
	$alter=0;
	if($_SESSION['vhdl_lang']=='gr'){
		$col_offset = '3';
		$col_size = '2';
	}else{
		$col_offset = '5';
		$col_size = '1';
	}
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
				<a href="<?php echo $link_edit; ?>" class="col-sm-offset-<?php echo $col_offset ?> col-sm-<?php echo $col_size ?> btn btn-info"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_5'] ?></a>
				<form class="form" action="" method="post" onsubmit="confirm_user_removal_admin()" >
					<input type="hidden" name="user_id" value="<?php echo $found_user['id']; ?>" />
					<button type="submit" class="col-sm-offset-1 col-sm-<?php echo $col_size ?> btn btn-danger" name="post_action" value="Remove_User_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_choice_4'] ?></button>
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
				<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_1'] ?></h3>
			  </div>
			  <div class="modal-body tab-content">
				<form class="form" id="form_register" role="register" action="" method="post" data-toggle="validator" novalidate="false">
					<div class="form-group">
						<label for="email"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_1'] ?> :</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="Email address" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_2'] ?>" required/>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<label for="username"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_2'] ?> :</label>
						<input type="text" class="form-control" name="username" id="username_reg" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_2'] ?>" data-minlength="5" required/>
						<div class="help-block"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?></div>
					</div>
					<div class="form-group">
						<label for="active"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_2'] ?> :</label>
						<select name="active">
							<option value="1" selected><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_3'] ?></option>
							<option value="0"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_4'] ?></option>
						</select>
						<div class="help-block"></div>
					</div>
					<div class="form-group">
						<label for="type"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_5'] ?> :</label>
						<select name="type">
							<option value="0" selected><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_6'] ?></option>
							<option value="1"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_7'] ?></option>
						</select>
						<div class="help-block"></div>
					</div>
					<div class="form-group">
						<label for="password"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_3'] ?> :</label>
						<input type="password" class="form-control" name="password" id="password_reg" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_3'] ?>" data-minlength="5" required/>
						<div class="help-block"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?></div>
					</div>
					<div class="form-group">
						<label for="password_confirm"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_4'] ?> :</label>
						<input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_4'] ?>" data-match="#password_reg" data-match-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_3'] ?>" required/>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<label for="telephone"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_5'] ?>:</label>
						<input type="telephone" class="form-control" name="telephone" id="telephone" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_5'] ?>" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_4'] ?>" data-minlength="10" />
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<button type="submit" id="register_btn" class="btn btn-primary" name="post_action" value="Create_User_Admin"><?php echo $messages->text[$_SESSION['vhdl_lang']]['admin_new_user_8'] ?></button>
					</div>
				</form>
				
			  </div>
			  <div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo $messages->text[$_SESSION['vhdl_lang']]['project_dir_22'] ?></button>
			  </div>
			</div>
		</div>
	</div>
</div>