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
	<h1>HDL Everywhere</h1>
	<h2>Welcome to the web based VHDL Compiler &amp; Simulator!</h2>
</div>

<div class="row">
	<div class="col-sm-4 col-md-offset-4">
		<ul class="nav nav-tabs">
			<li class="active"><a data-toggle="tab" href="#login-from">Log In</a></li>
			<li><a data-toggle="tab" href="#sid-from">SID</a></li>
			<li><a data-toggle="tab" href="#register-from">Register</a></li>
		</ul>
		<div class="row tab-content" >
			<div id="login-from" class="tab-pane fade in active">
				<h2>Log In</h2>
				<form class="form" role="login" method="post" action="">
					<div class="form-group">
						<label for="username">Username :</label>
						<input type="text" name="username" class="form-control" placeholder="Username" />
					</div>
					<div class="form-group">
						<label for="password">Password :</label>
						<input type="password" name="password" class="form-control" placeholder="Password" />
					</div>
					<button type="submit" class="btn btn-default" name="post_action" value="login">Login</button>
				</form>
			</div>
			<div id="sid-from" class="tab-pane fade">
				<h2>SID</h2>
				<form class="form" role="login-sid" method="post" action="">
					<div class="form-group">
						<label for="pid">SID :</label>
						<input type="text" name="pid" class="form-control" size='8' value="<?php echo (!empty($_SESSION['PID'])) ? $_SESSION['PID']:''; ?>" placeholder="SID" />
						<button type="submit" class="btn btn-default" name="post_action" value='set_sid'>Set SessionID</button>
					</div>
					<button type="submit" class="btn btn-default" name="post_action" value='new_sid'>Create New Random SessionID</button>
				</form>
			</div>
			<div id="register-from" class="tab-pane fade">
				<h2>Register</h2>
				<form class="form" role="register" action="" method="post">
					<div class="form-group">
						 <label for="email">Email address :</label>
						 <input type="email" class="form-control" name="email" id="email" placeholder="Email address" required="">
					</div>
					<div class="form-group">
						 <label for="username">Username :</label>
						 <input type="text" class="form-control" name="username" id="username" placeholder="Username" required="">
					</div>
					<div class="form-group">
						 <label for="password">Password :</label>
						 <input type="password" class="form-control" name="password" id="password" placeholder="Password" required="">
					</div>
					<div class="form-group">
						 <label for="password_confirm">Confirm Password :</label>
						 <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Confirm Password" required="">
					</div>
					<div class="form-group">
						 <button type="submit" class="btn btn-default" name="post_action" value="register">Register</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

