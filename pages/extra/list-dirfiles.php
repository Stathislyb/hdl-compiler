<ul class="list-group list-files">
	<?php
	if( count($files)==0 ){
		echo "<li class='list-group-item'> There are no files in this directory </li>";
	}
	foreach ($files as $file) {
		if($file['relative_path'] == "/"){
			$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type']."/".$file['name'];
		}else{
			$path = $BASE_URL."/project/".$search_user['username']."/".$_GET['project']."/".$file['type'].$file['relative_path']."/".$file['name'];
		}
		echo "<li class='list-group-item'><a href='".$path."'>".$file['name']."</a>";
		echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
	}
	if($user->validate_edit_rights($editors)){ ?>
		<li class='list-group-item'>
			<center>
				<span class="glyphicon glyphicon-plus-sign pointer strong" aria-hidden="true" data-toggle="modal" data-target="#Create-filedir-Modal"></span>
			</center>
		</li>
	<?php }?>
</ul>