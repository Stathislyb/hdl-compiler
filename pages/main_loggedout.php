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
	<div class="col-sm-6">
		<?php require('pages/extra/list-new-projects.php'); ?>
	</div>
	<div class="col-sm-6">
		list 2
	</div>
</div>


<div class="row">
	Status:
	<?php require('status.php'); ?>
</div>

<div class="row">
	Step1
	<div class="topic1" id="pid">
	<?php require('sid.php');?>
	</div>
</div>


<?php 
//Only show the rest of the forms if PID is set
if (empty($_SESSION['PID'])){
	$_SESSION['PID']=0; 
	print '<tr><td colspan="2"><div class="red">No session set. Please Set or Create a SessionID!</div></td></tr>';
}else{
	echo 'Step2';
	echo '<div class="topic2" id="files">';
	require('files.php');
	echo '</div>';


	echo 'Step3<div class="topic1" id="listfiles">';
	require('listfiles.php');
	echo '</div>';

	echo '<div class="topic2" id="link">';
	require('link.php');
	echo '</div>';
}

?>
