<?php
class General {
	
	// Class constractor function
	//  initialise the user variables through the session
	public function __construct(){
	}
	public function __destruct(){
	}
	
	// Return the directory in the correct format
	public function clear_dir($dir){
		if(empty($dir)){
			$dir="/";
		}
		if($dir!="/"){
			$dir = "/".$dir;
			$dir = rtrim($dir, "/");
		}
		return $dir;
	}
	
	// Return path of links to the currect directory
	public function path_to_links($full_path, $user, $project, $BASE_URL){
		$output = '';
		$path = explode('/',$full_path);
		foreach ($path as $key=>$step) {
			$link_path = '';
			for($i=0;$i<=$key;$i++){
				if($i!=0 && $i!=$key){
					$link_path .= $path[$i]."/";
				}else{
					$link_path .= $path[$i];
				}
			}
			$link = $BASE_URL."/project/".$user."/".$project."/directory/".$link_path;
			if($key == 0){
				$output .= "<a href='".$link."'>".$_GET['project']."/ </a>";
			}else{
				if(!empty($step)){
					$output .= "<a href='".$link."'>".$step."/ </a>";
				}
			}
		}
		return $output;
	}
	
}
?>