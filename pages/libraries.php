<?php 
	if( empty($_GET['short_code']) ){
		require('pages/extra/list-all-libraries.php');
	}else{
		require('pages/extra/display-library.php');
	}
	
?>