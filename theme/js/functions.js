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

var auto_refresh = setInterval(
function ()
{
//$('#statusDIV').load('status.php').fadeIn("slow");
}, 5000); // refresh every 5000 milliseconds

//Add Event listeners when the document is fully loaded
$( document ).ready(function() {
	
	// Handle Navbar Search type change
	$("#search-nav-dropdown-ul li a").click(function(){
		var Choice = $(this).text();
		$("#search-nav-dropdown").html(Choice+'<span class="caret"></span>');
		$("#nav-search-type").val(Choice);
		$("#nav-search").val('');
	});
	
});