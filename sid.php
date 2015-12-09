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

//var_dump($_POST);

if(isset($_POST["bset"]))
{
//var_dump($_POST);
if($_POST['bset']==0) 	
	{
	echo "CLEARPID";
	$pid="0";
	unset($_SESSION['PID']);
	setcookie("PID", 0, time()-3600);
	reload_page();
	} 

if($_POST['bset']==1)	
	{
	$_SESSION['PID']=intval($_POST['pid']);
	setcookie("PID", $_SESSION['PID'], time()+3600);
	reload_page();
	}

if($_POST['bset']==2)	{srand();
			$number=rand()%100000;
			while(!mkdir($BASE.$number,0700))
			{
			$number=rand()%100000;
			}
			// Ok we found a new random file
			$_SESSION['PID']=$number;
			setcookie("PID", $_SESSION['PID'], time()+3600);
			reload_page();
			}

//If we had a post on bset, then we should do a full page reload
}
?>



<?php 
if (isset($_SESSION['PID'])) { $pid=$_SESSION['PID'];} else { $pid ="0";}
?>

<form action='' method='post'>
SID:<input type="text" name="pid" size='8' value="<?php echo $pid; ?>" />
<br />
<button type="submit" name="bset" value='0'>Clear SessionID</button>
<button type="submit" name="bset" value='1'>Set SessionID</button>
<button type="submit" name="bset" value='2'>Create New Random SessionID</button>
</form>
