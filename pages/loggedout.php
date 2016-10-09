<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php
/*
   Copyright 2014, Minas Dasygenis, http://arch.icte.uowm.gr/mdasygenis

   Licensed under the Apache License, Version 2.0 (the "License");
   you may not use this file except in compliance with the License.
   You may obtain a copy of the License at

       http://www.apache.org/licenses/LICENSE-2.0

   Unless required by applicable law or agreed to in writing, software
   distributed under the License is distributed on an "AS IS" BASIS,
   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
   See the License for the specific language governing permissions and
   limitations under the License.
*/
?>

<div class="jumbotron">
	<h1><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_header_1'] ?></h1>
	<h2><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_header_2'] ?></h2>
</div>

<div class="row">
	<div class="col-sm-4 col-sm-offset-4">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#login-from"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_log_in'] ?></a></li>
			<li><a data-toggle="tab" href="#sid-from"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_sid'] ?></a></li>
			<li><a data-toggle="tab" href="#register-from"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register'] ?></a></li>
		</ul>
		<div class="row tab-content" >
			<div id="login-from" class="tab-pane fade in active">
				<h2><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_log_in'] ?></h2>
				<form class="form" id="form_login" role="login" method="post" action="" data-toggle="validator">
					<div class="form-group">
						<label for="username"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_log_in_1'] ?> :</label>
						<input type="text" name="username" class="form-control" placeholder="Username" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?>" data-minlength="5" required/>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<label for="password"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_log_in_2'] ?> :</label>
						<input type="password" name="password" class="form-control" placeholder="Password" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?>" data-minlength="5" required/>
						<div class="help-block with-errors"></div>
					</div>
					<button type="submit" id="login_btn" class="btn btn-primary" name="post_action" value="login"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_log_in'] ?></button>
				</form>
			</div>
			<div id="sid-from" class="tab-pane fade">
				<h2>SID</h2>
				<form class="form" role="login-sid" method="post" action="">
					<div class="form-group">
						<label for="pid"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_sid'] ?> :</label>
						<input type="text" name="pid" class="form-control" size='8' value="<?php echo (!empty($_SESSION['PID'])) ? $_SESSION['PID']:''; ?>" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_sid'] ?>" />
						<button type="submit" class="btn btn-default" name="post_action" value='set_sid'><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_sid_1'] ?></button>
					</div>
					<button type="submit" class="btn btn-default" name="post_action" value='new_sid'><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_sid_2'] ?></button>
				</form>
			</div>
			<div id="register-from" class="tab-pane fade">
				<h2><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register'] ?></h2>
				<form class="form" id="form_register" role="register" action="" method="post" data-toggle="validator" novalidate="false">
					<div class="form-group">
						<label for="email"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_1'] ?> :</label>
						<input type="email" class="form-control" name="email" id="email" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_1'] ?>" data-error="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_2'] ?>" required/>
						<div class="help-block with-errors"></div>
					</div>
					<div class="form-group">
						<label for="username"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_2'] ?> :</label>
						<input type="text" class="form-control" name="username" id="username_reg" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register_2'] ?>" data-minlength="5" required/>
						<div class="help-block"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_dataerror_1'] ?></div>
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
						<button type="submit" id="register_btn" class="btn btn-primary" name="post_action" value="register"><?php echo $messages->text[$_SESSION['vhdl_lang']]['home_register'] ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

