	<nav class="navbar navbar-default navbar-top">
		<div class="container-fluid">
			<div class="collapse navbar-collapse pull-left" id="navbar-header">
				<ul class="nav navbar-nav">
					<li class="dropdown">
					  <a id="search-nav-dropdown" href="javascript:void(0)" class="dropdown-toggle" data-toggle="dropdown"><?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_search_1'] ?><span class="caret"></span></a>
					  <ul id="search-nav-dropdown-ul" class="dropdown-menu">
						<li><a href="javascript:void(0)"><?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_search_1'] ?></a></li>
						<li><a href="javascript:void(0)"><?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_search_2'] ?></a></li>
						<li><a href="javascript:void(0)"><?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_search_3'] ?></a></li>
					  </ul>
					</li>
					<li class="navbar-form">
						<input type="hidden" id="nav-search-type" value="Projects">
						<input type="text" class="form-control nav-search-typeahead" autocomplete="off" placeholder="<?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_search_4'] ?>" id="nav-search">
					</li>
				</ul>
				<form class="navbar-form pull-right" role="logout" method="post" action="">
					<?php if($_SESSION['vhdl_lang']=='gr'){ 
								$lang_image = "icon_United-Kingdom.png";
								$lang_value = "en";
							}else{
								$lang_image = "icon_Greece.png";
								$lang_value = "gr";
							}
					?>
					<input type="hidden" name="lang_value" value="<?php echo $lang_value ?>">
					<input type="image" height="30" width="44" src="<?php echo $BASE_URL ?>/theme/images/<?php echo $lang_image ?>" name="post_action" value="change_lang">
				</form>
			</div>
			<div class="navbar-user pull-right">
				<?php if(isset($_SESSION['vhdl_user']['loged_in']) && $_SESSION['vhdl_user']['loged_in']==1){ ?>
					<form class="navbar-form pull-right" role="logout" method="post" action="">
						<button type="submit" class="btn btn-default" name="post_action" value="logout"><?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_logout'] ?></button>
					</form>
					<div class="navbar-form pull-left nav-home">
						<a href="<?php echo $BASE_URL; ?>/settings" title="Settings"><span class="glyphicon glyphicon-cog top-5"></span></a>
						<a href="<?php echo $BASE_URL; ?>" ><?php echo $_SESSION['vhdl_user']['username']; ?></a>
					</div>
					<?php if($user->type=='1'){ ?>
						<form class="navbar-form pull-right admin-nav-form" method="post" action="<?php echo $BASE_URL; ?>/admin">
							<button type="submit" class="btn btn-default" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_admin'] ?></button>
						</form>
					<?php } ?>
				<?php }else{ ?>
					<div class="navbar-form pull-left nav-home">
						<a href="<?php echo $BASE_URL; ?>" ><?php echo $messages->text[$_SESSION['vhdl_lang']]['Nav_home'] ?></a>
					</div>
				<?php } ?>
			</div>
		</div>
    </nav>