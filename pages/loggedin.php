<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
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
if($_SESSION['vhdl_user']['activated']==1){
	require('pages/extra/list-user-projects.php'); 
	require('pages/extra/list-shared-projects.php'); 
}else{
	require('pages/activation.php'); 
}

?>


<div class="row">
	<div class="col-sm-6">
		<?php require('pages/extra/list-new-projects.php'); ?>
	</div>
	<div class="col-sm-6">
		<?php require('pages/extra/list-new-libraries.php'); ?>
	</div>
</div>

