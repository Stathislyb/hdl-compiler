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
				E-mail:<input type="text" name="email" size='25' /><br />
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
