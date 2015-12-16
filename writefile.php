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

//This file processes input and saves files
if (isset($_SESSION['PID'])) { $pid=$_SESSION['PID'];} else { $pid ="0";}

if ($pid<1)
{
return;
}


if (! $_POST)
{
return;
}

if( isset($_POST['filename']) && isset($_POST['contents'] ) )
{
$clean=dasygenis_filter_letters($_POST['filename']);
$file=$directory.$clean;
$contents=$_POST['contents'];
	if (file_exists($file)  && is_writable($file) )
	{
	$ret=file_put_contents($file,$contents);
	if(!$ret) { error_get_last();fail_500();}

	return;
	}//end if file exists
	else
	{
	fail_500();
	}
	

	

}//end if isset










?>
