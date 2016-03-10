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
		$project_shortcode = $this->create_short_code($project);
		$output = "<b><a href='".$BASE_URL."/project/".$user."'>".$user."> </a></b>";
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
			$link = $BASE_URL."/project/".$user."/".$project_shortcode."/directory/".$link_path;
			if($key == 0){
				$output .= "<a href='".$link."'>".$project."/ </a>";
			}else{
				if(!empty($step)){
					$output .= "<a href='".$link."'>".$step."/ </a>";
				}
			}
		}
		return $output;
	}
	
	// Return the shortcode of a string
	public function create_short_code($string){
		$replace_pairs = array(
			" " => "-",
			"α" => "a",
			"β" => "b",
			"γ" => "g",
			"δ" => "d",
			"ε" => "e",
			"ζ" => "z",
			"η" => "i",
			"θ" => "th",
			"ι" => "i",
			"κ" => "k",
			"λ" => "l",
			"μ" => "m",
			"ν" => "n",
			"ξ" => "ks",
			"ο" => "o",
			"π" => "p",
			"ρ" => "r",
			"σ" => "s",
			"τ" => "t",
			"υ" => "u",
			"φ" => "f",
			"χ" => "x",
			"ψ" => "ps",
			"ω" => "w",
			"Α" => "a",
			"Β" => "b",
			"Γ" => "g",
			"Δ" => "d",
			"Ε" => "e",
			"Ζ" => "z",
			"Η" => "i",
			"Θ" => "th",
			"Η" => "i",
			"Κ" => "k",
			"Λ" => "l",
			"Μ" => "m",
			"Ν" => "n",
			"Ξ" => "ks",
			"Ο" => "o",
			"Π" => "p",
			"Ρ" => "r",
			"Σ" => "s",
			"Τ" => "t",
			"Υ" => "u",
			"Φ" => "f",
			"Χ" => "x",
			"Ψ" => "ps",
			"Ω" => "o",
			"ά" => "a",
			"έ" => "e",
			"ή" => "i",
			"ί" => "i",
			"ό" => "o",
			"ύ" => "u",
			"ώ" => "o",
			"Ά" => "a",
			"Έ" => "e",
			"Ή" => "i",
			"Ί" => "i",
			"Ό" => "o",
			"Ύ" => "u",
			"Ώ" => "o"
		);
		$string = trim($string," ");
		$string = strtr($string, $replace_pairs);
		$string = strtolower($string);
		$string = preg_replace("/[^a-z0-9\-\.]/","",$string);
		$string = preg_replace("/-+/","-",$string);
		return $string;
	}
	
	// Return a list of editors comma seperated.
	public function list_editors_comma($editors){
		$list="";
		foreach($editors as $editor){
			$list.= $editor['username'].",";
		}
		return rtrim($list, ",");
	}
	
	// Return a list of editors with li format.
	public function list_editors_li($editors){
			$list="";
		foreach($editors as $editor){
			$list.= '<li class="list-group-item"><span class="editor-item">'.$editor['username'].'</span><span onclick="typeahead_remove_item(this)" class="glyphicon glyphicon-remove pull-right btn btn-danger btn-xs" aria-hidden="true"></span></li>';
		}
		return $list;
	}
	
	// Return part of string with about 160 characters.
	//   it's not exact because it considers cutting words in half and avoids it.
	public function fix_string_length($string){
		if( strlen($string) >105 ){
			$output = substr($string,0,105);
			$output = substr($output,0, strrpos($output,' ') )."...";
		}else{
			$output = $string;
		}
		return $output;
	}
	
	// Return the contents of a file.
	public function open_and_read_file($file){
		$output='';
		$file_handle = fopen($file, "r");
		while (!feof($file_handle)) {
		   $output .= fgets($file_handle);
		}
		fclose($file_handle);
		return $output;
	}
}
?>