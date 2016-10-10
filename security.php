<?php

$security_breach = false;

// Sanitize string POST data
$string_array = array('ajax_action','username','theme','query','name','directory','vcd_name','post_action','project_name',
						'original_user','project_description','projet_authors','download_vhdl','download_vcd','download_log',
						'file_name','owner','project_shortcode','upload_dir','architecture','extralib','extrasim',
						'password_edit','new_password_edit','rep_password_edit','password','password_confirm', 'search_type','code');
foreach($string_array as $string_element){
	if( isset($_POST[$string_element]) ){
		$security_temp = filter_var($_POST[$string_element], FILTER_SANITIZE_STRING);
		if( $_POST[$string_element] != $security_temp ){
			$security_breach = true;
		}
	}
}

// Sanitize integer POST data
$int_array = array('file_id','library_id','project_share','phone_edit','ace_theme','user_id_edit',
					'available_space_edit','user_id','telephone','active','type','pid');
foreach($int_array as $int_element){
	if( isset($_POST[$int_element]) ){
		$security_temp = filter_var($_POST[$int_element], FILTER_SANITIZE_NUMBER_INT);
		if( $_POST[$int_element] != $security_temp ){
			$security_breach = true;
		}
	}
}

// Sanitize special cases POST data
if( isset($_POST['project_id']) &&  $_POST['project_id']!="SID"){
	$security_temp = filter_var($_POST['project_id'], FILTER_SANITIZE_NUMBER_INT);
	if( $_POST['project_id'] != $security_temp ){
		$security_breach = true;
	}
}
if( isset($_POST['data']) ){
	$_POST['data'] = filter_var($_POST['data'], FILTER_SANITIZE_SPECIAL_CHARS);
}
if( isset($_POST['selected_ids']) ){
	$selected = explode('-',$_POST['selected_ids']);
	foreach ($selected as $file_id) {
		$file_id_sanitized = filter_var($file_id, FILTER_SANITIZE_NUMBER_INT);
		if($file_id_sanitized != $file_id){
			$security_breach = true;
		}		
	}
}
if( isset($_POST['email_edit'])) {
	$security_temp =  filter_var($_POST["email_edit"], FILTER_VALIDATE_EMAIL);
	if( $_POST['email_edit'] != $security_temp || strlen($_POST["email_edit"])>50 ){
		$security_breach = true;
		array_push($_SESSION['vhdl_msg'], 'invalid_mail');
	}
}
if( isset($_POST['email'])) {
	$security_temp =  filter_var($_POST["email"], FILTER_VALIDATE_EMAIL);
	if( $_POST['email'] != $security_temp || strlen($_POST["email"])>50 ){
		$security_breach = true;
		array_push($_SESSION['vhdl_msg'], 'invalid_mail');
	}
}

if( $security_breach ){
	array_push($_SESSION['vhdl_msg'], 'invalid_POST');
	header("Refresh:0");
	exit();
}

?>