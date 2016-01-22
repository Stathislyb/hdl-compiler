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
?>




<?php
if (isset($_SESSION['PID'])) { $pid=$_SESSION['PID'];} else { $pid ="0";}
?>

<?php

if ($pid>0){



//do we have data to process?
//var_dump($_POST);
if ($_POST)
{
	if( isset($_POST['architecture']))
	{
	$architectureclean=filter_var($_POST['architecture'],FILTER_SANITIZE_STRING);	
	$explodedarchitecture=explode(" ",$architectureclean);
	$unit=$explodedarchitecture[0];
	$architecture=$explodedarchitecture[1];	
	//echo "unit:".$unit." and architecture ".$architecture;
	check_and_create_job_directory();

	$extra_pre=process_pre_options($_POST);
	$extra_post=process_post_options($_POST);

	$prefile="--elab-run ".$extra_pre;
	$postfile=$architecture.$extra_post;
	create_job_file($directory,$unit,$prefile,$postfile);


	}


}




echo "<form  method='post' action=''>";
echo "<select name='architecture'>";



//we want here to display all valid architectures
$command="cd ".$directory.";/usr/bin/ghdl -d | grep architecture | awk ' {print $4,$2} '";
$output=shell_exec($command);

//Extract the lines
$lines = explode(PHP_EOL, $output);

//Iterate over every line
//and print option box
foreach($lines as $key => $value) {
	  if (!empty($value))
		{

		$string="<option value='".$value."'>".$value."</option>";
		echo $string;
           //print "$key => $value\n";
		}
	}

//var_dump($lines);
echo "</select>";
}

//print the ghdl options checkboxes
extra_ghdl_options();
extra_simulation_options();

echo "<button type='submit' name='submit' value='submit'>Simulate</button>";

//Display the end form, only if we have displayed the previous form
if ($pid>0){
echo "</form>";
}

?>
