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
session_dasygenis();
require_once('uploadclass.php');


error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);

global $BASE;
global $JOBDIRECTORY;
global $directory;
global $MAKEFILE_SUPPORT;


# Set here the variables
# --START--
$BASE="/tmp/VHDL/";
$JOBDIRECTORY="/tmp/jobs/";
$STATUSDIR="/tmp/status/";
$GLOBALS['MAKEFILE_SUPPORT']="0";
$GLOBALS['BASE']=$BASE;

# --END--





//Check the existance of the BASE directory and the JOBDIRECTORY
if ( ! file_exists($BASE) ) { mkdir($BASE,0777,true);echo "TEMP1_MKD"; } 
if ( ! file_exists($JOBDIRECTORY) ) { mkdir($JOBDIRECTORY,0777,true); echo "TEMP2_MKD2"; }
if ( ! file_exists($STATUSDIR) ) { mkdir($STATUSDIR,0777,true); echo "TEMP3_MKD3"; }



if(isset($_SESSION['PID']) && $_SESSION['PID']>0 ) 
{
$directory=$BASE.$_SESSION['PID']."/";
}






function process_pre_options($postarray)
{
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







function process_post_options($postarray)
{
$extra="";

if ( isset($postarray['extrasim'] ) )
	{
   foreach ( $_POST['extrasim'] as $value )
	{
       if ( $value == "vcd")
		{ $extra=$extra." --vcd=testbench.vcd "; }
	}



} //end if set

return $extra;
}











function check_and_delete_file($filenamedelete)
{

//echo "<br />Checking $filenamedelete <br />";
        if (file_exists($filenamedelete))
                {
                if ( unlink($filenamedelete) ) { echo "File ".basename($filenamedelete)." removed"; }
                }


}


function unzip_file_to_directory($file,$directory)
{
    $zip = new ZipArchive;
     $res = $zip->open($file);
     if ($res === TRUE) {
         $zip->extractTo($directory);
         $zip->close();
         //echo 'ZipUnzipped';
	 return 0;
     } else {
         //echo 'ZipFailUnzip';
	 return 1 ;
     }
 return 2;
}


//compile will happen at the directory
//file is the vhdl file
//prefile are the parameters before the file
//postfile are the parameters after the file

function create_job_file($directory,$file,$prefile,$postfile='',$executable='/usr/lib/ghdl/bin/ghdl',$timeout=6)
{
global $JOBDIRECTORY;
check_and_create_job_directory();
$results=get_latest_file_name($JOBDIRECTORY);
$last=$results[0];
$nrfiles=$results[1]-1;

if ( $last =="") 
	{
	//No files in current directory, so we put a new one there  
	$last=0;
	}

	//$counter=(intval($last)+1)%100;
	//$random=rand(1,1024);
	//$last=$JOBDIRECTORY.$counter.$random;
	$last=tempnam($JOBDIRECTORY,"VHDL");
	$jobid=basename($last);

        $cmd="cd '$directory' ;rm '$file'.log ;  ( '$executable' $prefile '$file' $postfile >> '$file'.log  2>&1 ) &  sleep ".$timeout."  ; killall -9 ".$file."-".$postfile."  ";
	$ourhandle=fopen($last,'w') or die ("cannot create job file");
	fwrite($ourhandle,$cmd); 
	echo "<div class='orangediv'>Created Job ID: $jobid. Queued Jobs $nrfiles.</div>";
	fclose($ourhandle);



                //$line=system("$cmd 2>&1",$retval);
                //echo $cmd;
                //var_export(my_exec("$cmd"));
                //echo "C $file $line $retval";





}



function get_latest_file_name($dir)
{
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




function check_and_create_job_directory()
{
global $JOBDIRECTORY;
if(!is_dir($JOBDIRECTORY)) { 
			if(!mkdir($JOBDIRECTORY,0700)) { die("Error in Creating Job Directory"); } 
			}

}

function my_exec($cmd, $input='') 
         {$proc=proc_open($cmd, array(0=>array('pipe', 'r'), 1=>array('pipe', 'w'), 2=>array('pipe', 'w')), $pipes); 
          fwrite($pipes[0], $input);fclose($pipes[0]); 
          $stdout=stream_get_contents($pipes[1]);fclose($pipes[1]); 
          $stderr=stream_get_contents($pipes[2]);fclose($pipes[2]); 
          $rtn=proc_close($proc); 
          return array('stdout'=>$stdout, 
                       'stderr'=>$stderr, 
                       'return'=>$rtn 
                      ); 
         } 



function isAvailable($func) {
    if (ini_get('safe_mode')) return false;
    $disabled = ini_get('disable_functions');
    if ($disabled) {
        $disabled = explode(',', $disabled);
        $disabled = array_map('trim', $disabled);
        return !in_array($func, $disabled);
    }
    return true;
}



// Initialize a new session and manage cookies

function session_dasygenis()
{
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if ( isset($_COOKIE['PID']) && $_COOKIE['PID'] > 0 ) 
	{ 
	   // echo "READ_COOKIE";
 	   $cleancookiepid = dasygenis_filter_letters($_COOKIE['PID']);
	   $_SESSION['PID'] = $cleancookiepid;
	}
else
   {
//cookie is not set, but session is set
	if ( isset($_SESSION['PID']) && $_SESSION['PID'] > 0 )
	{ $_COOKIE['PID'] = $_SESSION['PID'] ;
	  setcookie("PID", $_SESSION['PID'], time()+36000);
	  //echo "WRITE_COOKIE";
	}
	
   }
	
	if( !( isset($_SESSION['vhdl_msg']) && is_array($_SESSION['vhdl_msg']) ) ){
		$_SESSION['vhdl_msg'] = array();
	}

} //end function session dasygenis






  function tempdir($dir, $prefix='', $mode=0700)
  {
    if (substr($dir, -1) != '/') $dir .= '/';

    do
    {
      $path = $dir.$prefix.mt_rand(0, 9999999);
    } while (!mkdir($path, $mode));

    return $path;
  }




function get_status()
{
global $STATUSDIR;

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($STATUSDIR),
RecursiveIteratorIterator::SELF_FIRST);
$it->rewind();
echo "<pre>";
while($it->valid()) {
		if (!$it->isDot() && !$it->isDir()) {
		$file=$it->getSubPathName();
		$statusfile=$STATUSDIR.$file;
		//Note: $file holds the filename, which is also the pid of the worker
		echo "Worker Status [pid ".$file."]: <br />";
                $filecontents = file_get_contents($statusfile);
                print $filecontents;
		}
		//outside the if loop, to increase the counter
		$it->next();
}
echo "</pre>";


}


//print some hdl options
function extra_ghdl_options()
{
echo "<br />";
echo "<input type='checkbox' name='extralib[]' value='synopsys' />Include synopsis library for more primary units.<br />";
}

function extra_simulation_options()
{
echo "<input type='checkbox' name='extrasim[]' value='vcd' />Create a value changed dump VCD wave trace file.<br />";
}



//This function enters the directory, locates all VHD files and
//create job files to compile-one-by-one
//

function compile_all_files_in_directory($directory,$extraparameters,$timeout)
{


$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory),
RecursiveIteratorIterator::SELF_FIRST);
$it->rewind();


while($it->valid()) {
$ourfilename=$it->getSubPathName();
$ourfilenamewoextension=pathinfo($ourfilename)['filename'];
$name = pathinfo( $ourfilename )[ 'filename' ];
$ourextension=$it->getExtension();
$ourpath=$it->getSubPath();
$ourfullfilenamewoextension=$directory."/".$ourfilenamewoextension;
if (!$it->isDot() && !$it->isDir()) 
		{
		if( $ourextension=="vhd" || $ourextension=="VHD") {

		$file=$directory.$ourfilename;
                $prefile="-a ".$extraparameters;
                $postfile="";
		$executable="/usr/bin/ghdl";
                create_job_file($directory,$file,$prefile,$postfile,$executable,$timeout);
		//sleep for 0.25 sec
		time_nanosleep(0,150000000);
								}


		}

    //Next element
    $it->next();



	}//end while
} //end function



/* creates a compressed zip file */
function create_zip($files = array(),$destination = '',$overwrite = false) {
        //if the zip file already exists and overwrite is false, return false
        if(file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        //if files were passed in...
        if(is_array($files)) {
                //cycle through each file
                foreach($files as $file) {
                        //make sure the file exists
                        if(file_exists($file)) {
                                $valid_files[] = $file;
                        }
                }
        }
        //if we have good files...
        if(count($valid_files)) {
                //create the archive
                $zip = new ZipArchive();
                if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                        return false;
                }
                //add the files
                foreach($valid_files as $file) {
                        $zip->addFile($file,$file);
                }
                //debug
                //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

                //close the zip -- done!
                $zip->close();

                //check to make sure the file exists
                return file_exists($destination);
        }
        else
        {
                return false;
        }
}









// Function to create a zip file and send everything to user
function zipsend_all_files_in_directory($directory)
{

//we enter the directory, because we want a flat structure
chdir($directory);


$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory),
RecursiveIteratorIterator::SELF_FIRST);
$it->rewind();

$filelist=array();

//zip file will be created to basedir
$temp_file_to_send=tempnam($GLOBALS['BASE'],$_SESSION['PID'] );


while($it->valid()) {
$ourfilename=$it->getSubPathName();
$ourfilenamewoextension=pathinfo($ourfilename)['filename'];
$name = pathinfo( $ourfilename )[ 'filename' ];
$ourextension=$it->getExtension();
$ourpath=$it->getSubPath();
$ourfullfilenamewoextension=$directory."/".$ourfilenamewoextension;
if (!$it->isDot() && !$it->isDir()  && $it->getSize() >0 )
                {

	
	$filelist[]=$ourfilename;
	




	}//end if file
//Next element
$it->next();
}//end while files


//OK, filelist is ready lets send it


$result=create_zip($filelist,$temp_file_to_send.".zip");
if ( $result )
{ echo "Zip file created.<br /> Download it at:
[<a href=files/".basename($temp_file_to_send).".zip>Custom CPU Core</a>]";
}
else
{
echo "Strange error. Zip file could not be created. Please retry";
}




}//end function


//Create a clean function that accepts only letters
function dasygenis_filter_letters($string)
{
$var=filter_var($string,FILTER_SANITIZE_STRING);
$var2=preg_replace("/\.\./",".",$var);
$var3=preg_replace("/[^a-zA-Z0-9.,+_-]+/i", "-", $var2);
return $var2;
}





//Create a new button
function create_new_button()
{
echo "<button type='submit' name='new' value='1'>New VHDL file</button>";
}



//Create an edit button
function create_edit_button($filename)
{
echo "<button type='submit' name='edit' value='".$filename."'>Edit</button>";
//echo "<button type='submit' name='edit' value='../../../etc/passwd'>Edit</button>";
}


//Create a remove button
function create_remove_button($filename)
{
echo "<button type='submit' name='remove' value='".$filename."'>Remove</button>";
}



//Create a compile button
function create_compile_button($filename)
{
echo "<button type='submit' name='compile' value='".$filename."'>Compile</button>";
}




//Create a refresh button
function create_refresh_button()
{
echo "<button type='submit' name='refresh' value='0'>Refresh/Update Page.</button>";
}





//Create an href for download for this specific file
function create_href_for_download($relativedir,$filename)
{
echo "<td><a href=".$relativedir.$filename.">".$filename."</a></td>";
}





//Function to open a new page for edit
function edit_file($filename)
{

$file=dasygenis_filter_letters($filename);
if (file_exists($file)  && is_readable ($file) )
{
echo '<div id="editor">';
open_and_print_a_file($file);
echo '</div>';
//also the save button
echo '<div id="editor_buttons">';
print_save_button($file);
print_close_editor_window_button($file);
echo "</div><!-- end of buttons -->";
print_ace_javascript_code();
}



}



//Function to print file
function open_and_print_a_file($file)
{
$file_handle = fopen($file, "r");
while (!feof($file_handle)) {
   $line = fgets($file_handle);
   printf("%s",$line);
}
fclose($file_handle);

}



//Function to print save button
function print_save_button($file)
{
$file=dasygenis_filter_letters($file);
echo "Editing: ".basename($file)."<div id='edit_status'></div>";
if (file_exists($file)  && is_writable($file) )
{

$basefilename=basename($file);

$str=<<< FUNCTION


<script type="text/javascript" charset="utf-8">
saveFile = function() {
var myElem = document.getElementById('editor');
if (myElem == null) {
	 alert('Nothing to save yet.');
	 return;
	 }


//Ok we are good to go
var editor = ace.edit("editor");
var contents = editor.getSession().getValue();


  $.post("writefile.php",
            {contents: contents,
             filename: "{$basefilename}" },
            function() {
                    // add error checking


		var textContent = document.getElementById('edit_status');
		textContent.innerHTML="Saved!";

		
			setTimeout(function() {
			var textContent = document.getElementById('edit_status');
			textContent.innerHTML="";
    			//$('#edit_status').fadeOut('fast');
			}, 7000); // 7 secs

                });




}<!-- end javascript function -->
</script>


<!-- Button after the function for visibility -->

<input type='button' id="redbutton" 
name='save' value='Save {$basefilename}' onClick='saveFile()'>
FUNCTION;

//Print the string
echo $str;

}//end if exists
else
{
echo "FAIL";
var_dump(error_get_last());
}

}//end function










//Function to close the window
function print_close_editor_window_button()
{

$str=<<< BUTTON
<script type="text/javascript" charset="utf-8">
close_editor_div = function() {
document.getElementById('editor').style.display = 'none';
document.getElementById('editor_buttons').style.display = 'none';
}

enable_editor_div = function() {
document.getElementById('editor').style.display = 'block';
document.getElementById('editor_buttons').style.display = 'block';
}

</script>

<input type='button' id="greenbutton" name='close' value='Close Editor' onClick='close_editor_div()'>

<!-- We do not use this button any more
<input type='button' id="greenbutton" name='open' value='Open Editor' onClick='enable_editor_div()'>
-->

BUTTON;

echo $str;


}








//Function to print ACE javascript
function print_ace_javascript_code()
{
$string= <<< ACEJS

<script src="src/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
    var editor = ace.edit("editor");
    editor.setTheme("ace/theme/monokai");
    editor.getSession().setMode("ace/mode/vhdl");
</script>

ACEJS;

echo $string;

}






//Function to create a new file at current dir
function create_new_file_at_workdir()
{
global $directory;
$filename=tempnam($directory,"VHDL_");

$contents="-- Dasygenis VHDL Compiler";
file_put_contents($filename,$contents);

$newfile=$filename.".vhd";

echo "Created file: $newfile";
rename($filename,$newfile);
reload_page();


}



//Fail with error message 500, used at ajax calls
function fail_500()
{
header('HTTP/1.1 500 Internal Server Error', true, 500);
}


function reload_page()
{
//The reload should happen only once
if(! isset($_SESSION['reload']) )
{ 
echo "<script async='async' type='text/javascript'>window.location.reload(true); </script>";
$_SESSION['reload']=1;
}
else
{
//echo "NO RELOAD";
//We already in the reload section so just unset it
//so next time we will be ok
unset($_SESSION['reload']);
}

}
?>
