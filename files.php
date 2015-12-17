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


if(!empty($_FILES['userfile']['name']))
{
	global $BASE;
	$uploaddir=$BASE.$_SESSION['PID']."/"; 
	$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

	echo "<p>";

//Check for errors
 if ($_FILES['userfile']['error'] === UPLOAD_ERR_OK) { 
//uploading successfully done 
} else { 
throw new UploadException($_FILES['userfile']['error']); 
} 


	$ret=move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile);
	if ($ret) {
  	echo "Successfully uploaded.\n";
	} else {
	print_r(error_get_last());
   	echo "Upload failed. ErrorCode:".$ret;
	}

	$checkfile = pathinfo($uploadfile);
if ( $checkfile['extension'] == "zip" || $checkfile['extension'] == "ZIP" )
	{
	//Check to see if this is a zip file. It it is a zipfile then unzip it
	$ret=unzip_file_to_directory($uploadfile,$uploaddir);
	if ($ret==0) 
		{
		//file uploaded, just delete it
		unlink($uploadfile);
		}
	elseif ($ret==1)
		{
		//file could not be uploaded
		echo "Error in Unzipping";
		}
	}

	//echo "</p>";
	//echo '<pre>';
	//echo 'Here is some more debugging info:';
	//print_r($_FILES);
	//print "</pre>";
}

?>
<form enctype="multipart/form-data" action="" method="post">
<input type="hidden" name="MAX_FILE_SIZE" value="32000000" />
UploadFile: <input name="userfile" type="file" />
<input type="submit" value="Send File" />
</form>

