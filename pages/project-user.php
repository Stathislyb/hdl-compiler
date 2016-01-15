<?php 
	$search_user = $db->get_user_information($_GET['user'],"username");
	if($search_user['id'] != $user->id){
		$projects = $db->get_user_projects($search_user['id']);
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
						<br />".$project['description']."<br /><br />";

				}
			?>
			<br/>
		</div>
	</td>
</tr>
<?php
	}else{
		header('location:'.$BASE_URL);
	}		
?>