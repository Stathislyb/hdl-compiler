<?php
class General {
	
	// Class constractor function
	//  initialise the user variables through the session
	function __construct(){
	}
	function __destruct(){
	}
	
	
	// Return the shortcode of a string
	function create_short_code($string){
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
	function list_editors_comma($editors){
		$list="";
		foreach($editors as $editor){
			$list.= $editor['username'].",";
		}
		return rtrim($list, ",");
	}
	
	// Return a list of editors with li format.
	function list_editors_li($editors){
		$list="";
		foreach($editors as $editor){
			if( $editor['user_type']==1 ){
				$list.= '<li class="list-group-item"><span class="editor-item">'.$editor['username'].'</span></li>';
			}else{
				$list.= '<li class="list-group-item"><span class="editor-item">'.$editor['username'].'</span><span onclick="typeahead_remove_item(this)" class="glyphicon glyphicon-remove pull-right btn btn-danger btn-xs" aria-hidden="true"></span></li>';
			}
		}
		return $list;
	}
	
	// Return part of string with about 160 characters.
	//   it's not exact because it considers cutting words in half and avoids it.
	function fix_string_length($string){
		if( strlen($string) >105 ){
			$output = substr($string,0,105);
			$output = substr($output,0, strrpos($output,' ') )."...";
		}else{
			$output = $string;
		}
		return $output;
	}
	
	// Return the contents of a file.
	function open_and_read_file($file){
		$output='';
		$file_handle = fopen($file, "r");
		while (!feof($file_handle)) {
		   $output .= fgets($file_handle);
		}
		fclose($file_handle);
		return $output;
	}
	
	// Extract file and update the database
	function extract_file($file, $directory, $project_id){
		$zip = new ZipArchive;
		$db = new Database;
		
		if ($zip->open($file) === true) {
			for($i = 0; $i < $zip->numFiles; $i++) {
				$filearray = $zip->getNameIndex($i);
				$fileinfo = pathinfo($filearray);
				$absolute_dir = $directory;
				
				if( isset( $fileinfo['extension'] ) ){
					if( $db->add_file($fileinfo['basename'], $project_id) >0 ){
						copy("zip://".$file."#".$filearray, $absolute_dir."/".$fileinfo['basename']);
					}else{
						return false;
					}
				}
			}                   
			$zip->close();
			return true;
		}
		return false;
	}
	
	// Extract file and update the database for SID
	function extract_file_sid($file, $directory, $sid){
		$zip = new ZipArchive;
		$db = new Database;
		
		if ($zip->open($file) === true) {
			for($i = 0; $i < $zip->numFiles; $i++) {
				$filearray = $zip->getNameIndex($i);
				$fileinfo = pathinfo($filearray);
				$absolute_dir = $directory.$fileinfo['dirname'];
				if( $fileinfo['dirname'] == "." ){
					if( isset( $fileinfo['extension'] ) ){
						if( $db->add_sid_file($fileinfo['basename'], $sid) >0 ){
							copy("zip://".$file."#".$filearray, $absolute_dir."/".$fileinfo['basename']);
						}else{
							return false;
						}
					}
				}
			}                   
			$zip->close();
			return true;
		}
		return false;
	}
	
	// Filter string for invalide characters
	function filter_letters($string){
		$var=filter_var($string,FILTER_SANITIZE_STRING);
		$var2=preg_replace("/\.\./",".",$var);
		return $var2;
	}
	
	// Generate random user activation code
	function generate_code(){
		$char_array="ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$code="";
		for ($i = 1; $i <= 6; $i++) {
			$code .= $char_array[rand(0, strlen($char_array)-1)];
		} 
		return $code;
	}
	
	
	// Send SMS to mobile
	function send_sms($message, $mobile){
		$url = 'http://vlsi.gr/sms/webservice/process.php';
		$data = array('authcode' => '2002415', 'mobilenr' => $mobile,'message'=>$message);
		$options = array(
			'http' => array(
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data),
			),
		);
		$context  = stream_context_create($options);
		$result = file_get_contents($url, false, $context);
		return ($result===false) ? false : true;
	}
	
	// Send Email 
	function send_email($message, $subject, $mail){
		$headers = 'From: noreply@spam.vlsi.gr'."\r\n".'Reply-To: noreply@spam.vlsi.gr'."\r\n".'X-Mailer: PHP/'.phpversion();
		return mail($mail, $subject, $message, $headers);
	}
	
	// Return an array of the files in the path that match the file types provided 
	function get_directory_files($path, $file_types, $filename){
		$files = array($filename);
		$scanned_files = array_diff(scandir($path), array('..', '.'));
		foreach ($scanned_files as $scanned_file) {
			$tmp_path = $path.$scanned_file."/";
			if(is_dir($tmp_path)){
				$inner_files = $this->get_directory_files($tmp_path, $file_types, $scanned_file);
				array_push( $files, $inner_files );
			}else{
				foreach ($file_types as $type) {
					if(substr($scanned_file, -strlen($type)) == $type){
						array_push($files,$scanned_file);
					}
				}
			}
		}
		//var_dump($files);
		//echo "<br/><br/>";
		return $files;
	}
	
	// Adds files from the list to the zip object 
	function addfiles_to_zip($zip,$files,$path,$folder_name){
		foreach($files as $file) {
			if(is_array($file)){
				$inner_folder_name = ($folder_name !='')? $folder_name.$file[0]."/" : $file[0]."/";
				$inner_path = $path.$file[0]."/";
				array_shift($file);
				$this->addfiles_to_zip($zip,$file,$inner_path,$inner_folder_name);
			}else{
				$file_path = $path.$file;
				if(file_exists($file_path) && is_readable($file_path)){
					$zip->addFile($file_path, $folder_name.$file);
				}
			}
		}
		return;
	}
	
	//compile will happen at the directory
	//file is the vhdl file
	//prefile are the parameters before the file
	//postfile are the parameters after the file
	function create_job_file($JOBDIRECTORY,$directory,$file,$prefile,$postfile='',$executable='/usr/lib/ghdl/bin/ghdl',$timeout=6){
		$results=$this->get_latest_file_name($JOBDIRECTORY);
		$last=$results[0];
		$nrfiles=$results[1]-1;

		if ( $last ==""){
			//No files in current directory, so we put a new one there  
			$last=0;
		}
		
		$last=tempnam($JOBDIRECTORY,"VHDL");
		$jobid=basename($last);
		$cmd="cd '$directory' ;rm '$file'.log ;  ( '$executable' $prefile '$file' $postfile >> '$file'.log  2>&1 ) &  sleep ".$timeout."  ; killall -9 ".$file."-".$postfile."  ";
		$ourhandle=fopen($last,'w') or die ("cannot create job file");
		fwrite($ourhandle,$cmd); 
		
		fclose($ourhandle);
		
	}
	
	function get_latest_file_name($dir){
		$latest_ctime = 0;
		$latest_filename = '';    
		$path=$dir;
		$number_of_files=0;

		$d = dir($path);
		while (false !== ($entry = $d->read())) {
		  $number_of_files=$number_of_files+1;
		  $filepath = "{$path}/{$entry}";
		  // could do also other checks than just checking whether the entry is a file
		  if (is_file($filepath) && filectime($filepath) > $latest_ctime) {
			$latest_ctime = filectime($filepath);
			$latest_filename = $entry;
		  }
		}

		return array($latest_filename,$number_of_files);
	}
	
	function process_pre_options($postarray){
		$extra="";

		if ( isset($postarray['extralib'] ) )
		   {
		   foreach ( $_POST['extralib'] as $value )
			{
			   if ( $value == "synopsys")
					{ $extra=$extra." --ieee=synopsys "; }
			}
		   }//end if set

		return $extra;
	}

}
?>