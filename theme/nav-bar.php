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
				<input type="text" class="form-control nav-search-typeahead" autocomplete="off" placeholder="Search" id="nav-search">
			</li>
		</ul>
			
		<?php if(isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){ ?>
			<form class="navbar-form navbar-right" role="logout" method="post" action="">
				<button type="submit" class="btn btn-default" name="post_action" value="logout">Logout</button>
			</form>
			<div class="navbar-form navbar-right nav-home">
				<a href="<?php echo $BASE_URL; ?>" ><?php echo $_SESSION['vhdl_user']['username']; ?></a>
			</div>
		<?php } ?>
			
		</div>
		</div>
    </nav>