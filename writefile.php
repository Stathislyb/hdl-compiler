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
if (! $_POST){
	return;
}

if( isset($_POST['directory']) && isset($_POST['data'] ) ){
	$directory=dasygenis_filter_letters($_POST['directory']);
	$contents=$_POST['data'];
	if (file_exists($directory)  && is_writable($directory) ){
		$ret=file_put_contents($directory,$contents);
		if(!$ret) { error_get_last();fail_500();}
		echo "Changes saved.";
		return;
	}else{ //end if file exists
		echo "File does not exist.";
	}
	

	

}//end if isset










?>
