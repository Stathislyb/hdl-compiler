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
<?php
include('loader.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>HDL Everywhere: A web based VHDL Compiler &amp; Simulator</title>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
<meta http-equiv="Content-Style-Type" content="text/css" /> 
<meta http-equiv="Content-Script-Type" content="text/javascript" /> 
<link rel="stylesheet" type="text/css" href="index.css" media="screen" /> 

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/
libs/jquery/1.3.0/jquery.min.js"></script>

<script type="text/javascript" src="functions.js"></script>

</head>
<body id="body1" bgcolor="white" text="black">
<div id="Content">
<h1>HDL Everywhere</h1>
<h2>Welcome to the web based VHDL Compiler &amp; Simulator!</h2>

<table style="width: 100%;" cellpadding="0" cellspacing="10">
<tbody>
<tr class="data" valign="top">
<td colspan="2">
<div class="status">Status:
<?php require('status.php'); ?>
</div>
</td>
</tr>

<tr>
<td>
Step1
</td>

<td>
<div class="topic1" id="pid">
<?php require('sid.php');?>
</div>
</td>
</tr>
	
<tr>
	<td>
		Step 1 - Login
	</td>

	<td>
		<div class="topic1" id="login">
			<form action='' method='post'>
				Username:<input type="text" name="username" size='25' /><br />
				Password:<input type="password" name="password" size='25' />
				<br />
				<button type="submit" name="login" value='1'>Login</button>
			</form>
		</div>
	</td>
</tr>
	
<tr>
	<td>
		Step 2 - Register
	</td>

	<td>
		<div class="topic1" id="register">
			<form action='' method='post'>
				Username:<input type="text" name="username" size='25' /><br />
				Password:<input type="password" name="password" size='25' /><br />
				Confirm Password:<input type="password" name="password_confirm" size='25' />
				<br />
				<button type="submit" name="register" value='1'>Register</button>
			</form>
		</div>
	</td>
</tr>

<?php 
//Only show the rest of the forms if PID is set
	if (empty($_SESSION['PID'])) 
	{
	$_SESSION['PID']=0; 
	print '<tr><td colspan="2"><div class="red">No session set. Please Set or Create a SessionID!</div></td></tr>';
	}
else
{

echo '<tr><td>Step2</td><td>';
echo '<div class="topic2" id="files">';
require('files.php');
echo '</div></td></tr>';


echo '<tr><td>Step3</td><td><div class="topic1" id="listfiles">';
require('listfiles.php');
echo '</div></td></tt>';

echo '<tr><td>Step4</td><td><div class="topic2" id="link">';
require('link.php');
echo '</div></td></tr>';
	
}

?>


</tbody>
</table>
<div id="copyright">
Copyright 2014,<br />
Non-disclosed information for blind review
</div>

<div id="copyright2" style="visibility: hidden">
Copyright 2014, Minas Dasygenis, <a href="http://arch.icte.uowm.gr/mdasygenis"> http://arch.icte.uowm.gr/mdasygenis</a>.
<br />
Licensed under the Apache License, Version 2.0 (the "License").
http://www.apache.org/licenses/LICENSE-2.0
</div>
<div id="download">
The source code can be downloaded [<a href="..\webvhdl-trunk.tar.gz">Here</a>].

    <a href="http://validator.w3.org/check?uri=referer"><img
      src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" height="31" width="88" /></a>

 <a href="http://jigsaw.w3.org/css-validator/check/referer">
        <img style="border:0;width:88px;height:31px"
            src="http://jigsaw.w3.org/css-validator/images/vcss"
            alt="Valid CSS!" />
	</a>

</div>
</div>
</body>
</html>
