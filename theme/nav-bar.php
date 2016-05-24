	<nav class="navbar navbar-default navbar-top">
		<div class="container-fluid">
			<div class="collapse navbar-collapse pull-left" id="navbar-header">
				<ul class="nav navbar-nav">
					<li class="dropdown">
					  <a id="search-nav-dropdown" href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown">Projects<span class="caret"></span></a>
					  <ul id="search-nav-dropdown-ul" class="dropdown-menu">
						<li><a href="javascript:void(0)">Projects</a></li>
						<li><a href="javascript:void(0)">Users</a></li>
						<li><a href="javascript:void(0)">Components</a></li>
					  </ul>
					</li>
					<li class="navbar-form">
						<input type="hidden" id="nav-search-type" value="Projects">
						<input type="text" class="form-control nav-search-typeahead" autocomplete="off" placeholder="Search" id="nav-search">
					</li>
				</ul>
			</div>
			<div class="navbar-user pull-right">
				<?php if(isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){ ?>
					<form class="navbar-form pull-right" role="logout" method="post" action="">
						<button type="submit" class="btn btn-default" name="post_action" value="logout">Logout</button>
					</form>
					<div class="navbar-form pull-left nav-home">
						<a href="<?php echo $BASE_URL; ?>/settings" title="Settings"><span class="glyphicon glyphicon-cog top-5"></span></a>
						<a href="<?php echo $BASE_URL; ?>" ><?php echo $_SESSION['vhdl_user']['username']; ?></a>
					</div>
				<?php }else{ ?>
					<div class="navbar-form pull-left nav-home">
						<a href="<?php echo $BASE_URL; ?>" >Home</a>
					</div>
				<?php } ?>
			</div>
		</div>
    </nav>