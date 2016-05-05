<div class="row">
	<h4>Your account is not activated.</h4>
	<form class="form" id="form_activate" role="activate" method="post" action="" data-toggle="validator">
		<div class="form-group">
			<label for="code">Activation Code :</label>
			<input type="text" name="code" class="form-control" placeholder="Activation Code" data-error="The activation code needs to be 6 characters long" data-minlength="1" required/>
			<div class="help-block with-errors"></div>
		</div>
		<button type="submit" id="login_btn" class="btn btn-primary" name="post_action" value="activate">Activate</button>
	</form>
</div>