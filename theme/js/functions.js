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

//var auto_refresh = setInterval(function (){
//$('#statusDIV').load('status.php').fadeIn("slow");
//}, 5000); // refresh every 5000 milliseconds

//Add Event listeners when the document is fully loaded
$( document ).ready(function() {
	
	// Handle Navbar Search type change
	$("#search-nav-dropdown-ul li a").click(function(){
		var Choice = $(this).text();
		$("#search-nav-dropdown").html(Choice+'<span class="caret"></span>');
		$("#nav-search-type").val(Choice);
		$("#nav-search").val('');
	});
	
	
    $('#select_all').click(function(event) { 
        if(this.checked) { 
            $('.select_file').each(function() {
                this.checked = true;                
            });
        }else{
            $('.select_file').each(function() { 
                this.checked = false;                      
            });         
        }
    });
    
	$('#Selected_Action').submit(function(event) {
		var selected_ids='';
       	$('.select_file:checked').each(function() {
            selected_ids = selected_ids+"-"+$(this).val();                
        });
		if(selected_ids){
			selected_ids = selected_ids.substr(1);
			$('#selected_ids').val(selected_ids);
		}else{
			alert("Nothing selected");
			event.preventDefault();
			return false;
		}
    });
	
	$('#nav-search').on('keyup', function () {
		var query = $('#nav-search').val();
		var type = $('#nav-search-type').val();
		var formData = {ajax_action:"search_navbar",query:query,type:type};
		$.ajax({
			url : "/ajax_handler.php",
			type: "POST",
			data : formData,
			dataType:"json",
			success: function(data){
				var item;
				$('.nav-search-typeahead').typeahead('destroy');
				if(type == 'Projects'){
					item = '<li><a class="dropdown-item" href="#" onclick="navbar_search_project(this)" role="option"></a></li>';
				}else{
					item = '<li><a class="dropdown-item" onclick="navbar_search_user(this)" href="#" role="option"></a></li>';
				}
				$(".nav-search-typeahead").typeahead({source:data, autoSelect: true, delay:200, item:item});
			}
		});
    });
	
	$('#typeahead-input').on('keyup', function () {
		var query = $('#typeahead-input').val();
		var formData = {ajax_action:"select_users_like",username:query};
		$.ajax({
			url : "/ajax_handler.php",
			type: "POST",
			data : formData,
			dataType:"json",
			success: function(data){
				$('.typeahead').typeahead('destroy');
				$(".typeahead").typeahead({source:data, autoSelect: true, delay:200});
			}
		});
    });
	
	
});

function navbar_search_project(e) {
	var query = $(e).html();
	var formData = {ajax_action:"select_project_by_name",name:query};
	$.ajax({
		url : "/ajax_handler.php",
		type: "POST",
		data : formData,
		dataType:"json",
		success: function(data){
			var link = window.location.protocol + "//" + window.location.host + "/project/"+data['owner']+"/"+data['project'];
			window.location.replace(link);
		}
	});
};

function navbar_search_user(e) {
	var user = $(e).html();
	var link = window.location.protocol + "//" + window.location.host + "/project/"+user;
	window.location.replace(link);
};

function typeahead_update_value(e) {
	var user = $(e).html();
	$('#editor-users').append("<li class='list-group-item'><span class='editor-item'>"+user+"</span><span onclick='typeahead_remove_item(this)' class='glyphicon glyphicon-remove pull-right btn btn-danger btn-xs' aria-hidden='true'></span></li>");
	update_editors_field();
};

function typeahead_remove_item(e) {
	var user = $(e).html();
	$(e).parent().remove();
	update_editors_field();
};

function update_editors_field() {
	$("#projet_authors").val('');
	$( ".editor-item" ).each(function() {
		var user = $(this).html();
		$("#projet_authors").get(0).value += user+',';
	});
	var value = $("#projet_authors").val().slice(0, -1);
	$("#projet_authors").val(value) ;
};