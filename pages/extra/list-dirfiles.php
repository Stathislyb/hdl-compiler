<ul class="list-group list-files">
	<?php
	foreach ($files as $file) {
		if($file['relative_path'] == "/"){
			$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type']."/".$file['name'];
		}else{
			$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type'].$file['relative_path']."/".$file['name'];
		}
		echo "<li class='list-group-item'><a href='".$path."'>".$file['name']."</a>";
		echo "<span class='pull-right'><input type='checkbox' value='1' id='file_".$file['id']."'></span></li>";
	}
	?>
	<li class='list-group-item'>
		<center>
			<span id="create_dir_button" class="glyphicon glyphicon-plus-sign pointer strong" aria-hidden="true" data-toggle="modal" data-target="#Create-filedir-Modal"></span>
		</center>
	</li>
</ul>