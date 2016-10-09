<div class="row">
	<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['activation_1'] ?></h4>
	<form class="form" id="form_activate" role="activate" method="post" action="" data-toggle="validator">
		<div class="form-group">
			<label for="code"><?php echo $messages->text[$_SESSION['vhdl_lang']]['activation_2'] ?> :</label>
			<input type="text" name="code" class="form-control" placeholder="Activation Code" data-error="The activation code needs to be 6 characters long" data-minlength="1" required/>
			<div class="help-block with-errors"></div>
		</div>
		<button type="submit" id="login_btn" class="btn btn-primary" name="post_action" value="activate"><?php echo $messages->text[$_SESSION['vhdl_lang']]['activation_3'] ?></button>
	</form>
</div>