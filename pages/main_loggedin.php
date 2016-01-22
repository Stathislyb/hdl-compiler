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
	$projects = $db->get_user_projects($user->id);
	$shared_projects = $db->get_shared_projects($user->id);
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
	
<tr>
	<td>
		User
	</td>

	<td>
		<div class="topic1" id="user">
			<?php 
				foreach ($projects as $project) {

					echo "<a href='".$BASE_URL."/project/".$user->username."/".$project['short_code']."'>".$project['name']."</a> 
					(<a href='".$BASE_URL."/edit-project/".$project['short_code']."'>EDIT</a>) <br />".$project['description'].
					"<br /><br />";

				}
			?>
			<br/>
			<a href="<?php echo $BASE_URL; ?>/create-project">Create New Project</a>
		</div>
	</td>
</tr>
<?php 
	if( !empty($shared_projects) ){
?>
<tr>
	<td>
		Shared <br/>with User
	</td>

	<td>
		<div class="topic1" id="Shared">
			<?php 
				foreach ($shared_projects as $shared_project) {
					echo "<a href='".$BASE_URL."/project/".$shared_project['owner']."/".$shared_project['short_code']."'>".$shared_project['name']."</a> 
					(<a href='/project/".$shared_project['owner']."'>".$shared_project['owner']."</a>) <br />".$shared_project['description'].
					"<br /><br />";
				}
			?>
		</div>
	</td>
</tr>
<?php } ?>

