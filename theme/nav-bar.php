	<nav class="navbar navbar-default navbar-top">
		<div class="container-fluid">
		<div class="collapse navbar-collapse" id="navbar-header">
			
     	<ul class="nav navbar-nav">
			<li class="dropdown">
			  <a id="search-nav-dropdown" href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Projects<span class="caret"></span></a>
			  <ul id="search-nav-dropdown-ul" class="dropdown-menu">
				<li><a href="javascript:void(0)">Projects</a></li>
				<li><a href="javascript:void(0)">Users</a></li>
				<li><a href="javascript:void(0)">Libraries</a></li>
			  </ul>
			</li>
			<li class="navbar-form">
				<input type="hidden" id="nav-search-type" value="Projects">
				<input type="text" class="form-control" placeholder="Search" id="nav-search">
			</li>
		</ul>
			
		<?php if(isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){ ?>
			<form class="navbar-form navbar-right" role="logout" method="post" action="">
				<button type="submit" class="btn btn-default" name="post_action" value="logout">Logout</button>
			</form>
		<?php }else{ ?>
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown">
				  <a href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Register<span class="caret"></span></a>
				  <ul id="register-nav" class="dropdown-menu">
					<li>
						<div class="row">
							<div class="col-md-12">
						<form class="form" role="register" action="" method="post">
							<div class="form-group">
								 <label class="sr-only" for="email">Email address</label>
								 <input type="email" class="form-control" name="email" id="email" placeholder="Email address" required="">
							</div>
							<div class="form-group">
								 <label class="sr-only" for="username">Username</label>
								 <input type="text" class="form-control" name="username" id="username" placeholder="Username" required="">
							</div>
							<div class="form-group">
								 <label class="sr-only" for="password">Password</label>
								 <input type="password" class="form-control" name="password" id="password" placeholder="Password" required="">
							</div>
							<div class="form-group">
								 <label class="sr-only" for="password_confirm">Confirm Password</label>
								 <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="Confirm Password" required="">
							</div>
							<div class="form-group">
								 <button type="submit" class="btn btn-default" name="post_action" value="register">Register</button>
							</div>
						</form>
						</div></div>		
					</li>
				  </ul>
				</li>
			</ul>
			<form class="navbar-form navbar-right" role="login" method="post" action="">
				<input type="text" name="username" class="form-control" placeholder="Username" />
				<input type="password" name="password" class="form-control" placeholder="Password" />
				<button type="submit" class="btn btn-default" name="post_action" value="login">Login</button>
			</form>
		<?php } ?>
			
		</div>
		</div>
    </nav>