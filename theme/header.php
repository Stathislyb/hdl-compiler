<!DOCTYPE html>
<html>
<head>
	<title><?php echo $messages->text[$_SESSION['vhdl_lang']]['title'] ?></title>
	
	<meta charset="utf-8">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<meta http-equiv="Content-Style-Type" content="text/css" /> 
	<meta http-equiv="Content-Script-Type" content="text/javascript" /> 
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<link rel="stylesheet" type="text/css" href="<?php echo $BASE_URL ?>/theme/bootstrap/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $BASE_URL ?>/theme/bootstrap/bootstrap-theme.min.css">
	<link rel="stylesheet" type="text/css" href="<?php echo $BASE_URL ?>/theme/css/style.css" media="screen" /> 
	
	<script type="text/javascript">window.base_url="<?php echo $BASE_URL; ?>";</script>
	<script type="text/javascript" src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
	
</head>
	
<body>
	<?php require('nav-bar.php'); ?>
	<div id="general-popup" class="hidden"></div>
	<div class="container">

