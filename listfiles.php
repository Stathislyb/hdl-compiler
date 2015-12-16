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
require_once('functions.php');
session_dasygenis();

$directory=$BASE.$_SESSION['PID']."/";
$relativedirectory="files/".$_SESSION['PID']."/";

if ( isset($_SESSION['PID']) &&  ($_SESSION['PID']) && is_dir($directory))
{

if ($_POST)
{

//extra variable for extra compile options
$extra="";

//var_dump($_POST);

if( isset($_POST['compile'])) 
	{ 
	$clean=dasygenis_filter_letters($_POST['compile']);
	$file=$directory.$clean;
	if (file_exists($file))
		{
		check_and_create_job_directory();

		$extra=process_pre_options($_POST);

		$prefile="-a ".$extra;
		$postfile="";
		$executable='/usr/lib/ghdl/bin/ghdl';
		$timeout=6;
		create_job_file($directory,$file,$prefile,$postfile,$executable,$timeout);


		chdir($directory);
	
		//$line=system("$cmd 2>&1",$retval);
		//echo $cmd;
		//var_export(my_exec("$cmd"));
		//echo "C $file $line $retval"; 
		}
	}

//Next case
if (isset($_POST['new']))
{
create_new_file_at_workdir();

}

//Next case
if( isset($_POST['remove']))  
	{ 
	$clean=dasygenis_filter_letters($_POST['remove']);
	$file=$directory.$clean;
	check_and_delete_file($file);

	$objectfile=pathinfo( $file )[ 'filename' ].".o";
	check_and_delete_file($directory.$objectfile);
	$logfile=$file.".log";
	check_and_delete_file($logfile);
	}

//Next case
if( isset($_POST['view']))
        {
	$clean=dasygenis_filter_letters($_POST['view']);
        $file=$directory.$clean;
        if (file_exists($file))
                {
		echo "<pre>";
                $filecontents = file_get_contents($file);
		print $filecontents;
		echo "</pre>";
                }
        }

//Next case
if( isset($_POST['edit']))
	{
	$clean=dasygenis_filter_letters($_POST['edit']);
	$file=$directory.$clean;
	if (file_exists($file))
		{
		edit_file($file);
		}

	}


//Next case
if( isset($_POST['make']))
{

// Only if global enable makefile is set
if ( $GLOBALS['MAKEFILE_SUPPORT']==1 ) 
	{
        $clean=filter_var($_POST['make'],FILTER_SANITIZE_STRING);
        $file=$directory.$clean;
        if (file_exists($file))
                {
                check_and_create_job_directory();

                $prefile="-f";
                $postfile="all";
		$timeout=5;
                create_job_file($directory,$file,$prefile,$postfile,"/usr/bin/make",$timeout);
                }
	}
}

//Next case
if( isset($_POST['compileall']) ) 
{
	//Here we have to compile every single file
	$extraparameters=process_pre_options($_POST);
	$timeout=5;
	compile_all_files_in_directory($directory,$extraparameters,$timeout);
}



//Next case
if ( isset($_POST['downloadall']))
{
        //Here we have to create a zip file with everything
        zipsend_all_files_in_directory($directory);
}


//Next case
}//end if post


echo '<div class="white">Contents of: '.$_SESSION['PID']."/</div>";
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory),
RecursiveIteratorIterator::SELF_FIRST);
$it->rewind();



echo "<form action='' method='post'>";
echo "<table class='bottomBorder'>";
while($it->valid()) {
$ourfilename=$it->getSubPathName();
if ( !file_exists($directory."/".$ourfilename) ) { $it->next();  continue; } 
$ourfilenamewoextension=pathinfo($ourfilename)['filename'];
$name = pathinfo( $ourfilename )[ 'filename' ];
$ourextension=$it->getExtension();
$ourpath=$it->getSubPath();
$ourfullfilenamewoextension=$directory."/".$ourfilenamewoextension;

//echo "$ourpath + $ourextension ++ $ourfilenamewoextension  +++ $ourfilename<br />";

    if (!$it->isDot() && !$it->isDir()) {
echo "<tr>";

	// Check to see if this is a VHDL file
	if( $ourextension=="vhd" || $ourextension=="VHD") {
	create_href_for_download($relativedirectory,$ourfilename);

	echo "<td>";
	create_edit_button($ourfilename);
	create_compile_button($ourfilename);
	create_remove_button($ourfilename);
	echo "</td>";
	//check if also the .o file is present
	if ( file_exists($ourfullfilenamewoextension.".o") ) 
	{ echo "<td class='other2'>COMPILED</td>";}


	$logfile=$ourfilename.".log";
	$logfilefull=$directory."/".$logfile;

	// If there is a log file with the previous file, do print it here 
	if ( file_exists($logfilefull) && filesize($logfilefull) > 0 ) {
	echo "<td>";
	echo "<button type='submit' name='view' value='".$logfile."'>View Logfile</button>";
	echo "<button type='submit' name='remove' value='".$logfile."'>Remove Logfile</button><br />";
	echo "</td>";
	}

	echo "</tr>";
	}//end if vhdl



	// check to see if this is a Makefile
	if( $ourfilename== "Makefile" || $ourfilename=="MAKEFILE" )
	{

	if ( $GLOBALS['MAKEFILE_SUPPORT']==0 ) { $button="disabled"; } else { $button=""; }
	echo "<tr>";
	create_href_for_download($relativedirectory,$ourfilename);
	
	echo "<td>";
	echo "<button type='submit' name='make' value='".$ourfilename."' ".$button.">Make ".$button."</button>";
	echo "<button type='submit' name='remove' value='".$ourfilename."'>Remove</button>";
	echo "</td></tr>";
	}


	if($it->getExtension()=="vcd" && $it->getSize() >0 ) {

	echo "<tr class='other'>";
	create_href_for_download($relativedirectory,$ourfilename);
	echo "<td>";
	echo "<button type='submit' name='remove' value='".$ourfilename."'>Remove</button>";
	echo "</td>";
	echo "<td>Signal Trace</td>";
	echo "</tr>";
	}

	if($it->getExtension()=="log" && $it->getSize() >0 ) {
	echo "<tr class='other_light'>";
	create_href_for_download($relativedirectory,$ourfilename);
	echo "<td>";
	echo "<button type='submit' name='remove' value='".$ourfilename."'>Remove</button>";
	echo "</td>";
	echo "<td>Simulation Logfile</td>";
	echo "</tr>";

	}


        //echo 'SubPathName: ' . $it->getSubPathName() . "<br />";
        //echo 'SubPath:     ' . $it->getSubPath() . "<br />";
        //echo 'Key:         ' . $it->key() . "<br />\n\n";

echo "</td></tr>";
    }//end of if file

    //Next element
    $it->next();
}

echo "</table>";

create_refresh_button();

echo "<button type='submit' name='compileall' value='0'>Compile all files</button>";
echo "<button type='submit' name='downloadall' value='0'>Download all files</button>";

create_new_button();

//print the ghdl options checkboxes
extra_ghdl_options();

echo "</form>";
}

if (!is_dir($directory)) { 
	echo "<div class='red'>Session ".$_SESSION['PID']." does not exist!</div>";
	echo '<form method="post">';
	create_refresh_button(); 
	echo '</form>';
	}

?>
