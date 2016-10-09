<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<ul class="list-group list-files">
	<?php
	if( count($files)==0 ){
		echo "<li class='list-group-item'> ".$messages->text[$_SESSION['vhdl_lang']]['list_dirfiles_1']." </li>";
	}
	foreach ($files as $file) {
		$path = $BASE_URL."/file/".$file['name'];
		echo "<li class='list-group-item'><a href='".$path."'>".$file['name']."</a>";
		if($file['compiled']=='1'){
			$full_path = $BASE.$_SESSION['SID'].'/'.$file['name'];
			$log_file = $full_path.".log";
			$bin_file = str_replace(".vhdl", ".o", $full_path);
			if( file_exists($log_file) ){
				if( file_exists($bin_file) ){
					if( filesize($log_file) == 0 ){
						echo "<span class='compile-info compile-success'>".$messages->text[$_SESSION['vhdl_lang']]['list_dirfiles_3']."</span>";
						echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
						$is_compiled=true;
					}else{
						echo "<span class='compile-info compile-error'><a href='#error_compile_".$file['id']."' data-toggle='collapse'> ".$messages->text[$_SESSION['vhdl_lang']]['list_dirfiles_4']." <span class='glyphicon glyphicon-chevron-down'></span></a></span>";
						echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
						echo "<div id='error_compile_".$file['id']."' class='collapse'><pre class='alert-danger'>".file_get_contents($log_file)."</pre></div>";
					}
				}else{
					if( filesize($log_file) != 0 ){
						echo "<span class='compile-info compile-error'><a href='#error_compile_".$file['id']."' data-toggle='collapse'> ".$messages->text[$_SESSION['vhdl_lang']]['list_dirfiles_4']." <span class='glyphicon glyphicon-chevron-down'></span></a></span>";
						echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
						echo "<div id='error_compile_".$file['id']."' class='collapse'><pre class='alert-danger'>".file_get_contents($log_file)."</pre></div>";
					}
				}
			}else{
				echo "<span class='compile-info compile-pending'> ".$messages->text[$_SESSION['vhdl_lang']]['list_dirfiles_1']."</span>";
				echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
			}
		}elseif($file['compiled']=='2'){
			echo "<span class='compile-info compile-pending'> ".$messages->text[$_SESSION['vhdl_lang']]['list_dirfiles_6']."</span>";
			echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
		}else{
			echo "<span class='pull-right'><input type='checkbox' class='select_file' value='".$file['id']."' id='file_".$file['id']."'></span></li>";
		}
	} ?>
	<li class='list-group-item'>
		<center>
			<span class="glyphicon glyphicon-plus-sign pointer strong" aria-hidden="true" data-toggle="modal" data-target="#Create-filedir-Modal"></span>
		</center>
	</li>
</ul>