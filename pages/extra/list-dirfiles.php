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
		if($file['compiled']=='1'){
			$full_path = $BASE_DIR.$search_user['username'].'/'.$_GET['project'].'/'.$file['name'];
			$log_file = $full_path.".log";
			if( file_exists($log_file) ){
				if( filesize($log_file) == 0 ){
					echo "<span class='compile-info compile-success'>Compiled</span>";
					echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
				}else{
					echo "<span class='compile-info compile-error'><a href='#error_compile_".$file['id']."' data-toggle='collapse'> Compile Failed <span class='glyphicon glyphicon-chevron-down'></span></a></span>";
					echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
					echo "<div id='error_compile_".$file['id']."' class='collapse'><pre class='alert-danger'>".file_get_contents($log_file)."</pre></div>";
				}
			}else{
				echo "<span class='compile-info compile-pending'> Pending Compile</span>";
				echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
			}
		}else{
			echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
		}
	}
	if($user->validate_edit_rights($editors)){ ?>
		<li class='list-group-item'>
			<center>
				<span class="glyphicon glyphicon-plus-sign pointer strong" aria-hidden="true" data-toggle="modal" data-target="#Create-filedir-Modal"></span>
			</center>
		</li>
	<?php }?>
</ul>