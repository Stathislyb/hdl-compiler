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
		var clicked_button = $(document.activeElement).html();
		var selected_ids='';
       	$('.select_file:checked').each(function() {
            selected_ids = selected_ids+"-"+$(this).val();                
        });
		if(selected_ids){
			selected_ids = selected_ids.substr(1);
			$('#selected_ids').val(selected_ids);
		}else{
			event.preventDefault();
			var popup = $("#general-popup");
			popup.html("<center><h3>Warning</h3><hr/><br/><h4>No file selected</h4><br/></center>");
			popup.removeClass();
			popup.addClass('popup alert alert-danger');
			popup.show();
			setTimeout(function() { $("#general-popup").hide(); }, 3000);
			return false;
		}
		if(clicked_button=="Remove Selected"){
			if( !confirm('Are you sure that you want to remove the selected files ? ') ){
				event.preventDefault();
			}
		}
    });
	
	$('#Remove_Project_form').submit(function(event) {
		if( !confirm('Are you sure that you want to remove the project ? ') ){
			event.preventDefault();
		}
    });
	
	$('#download_project_form').submit(function(event) {
		$("#Download-Project-Modal").modal( 'hide' );
    });
	
	$('#nav-search').on('keyup', function () {
		var query = $('#nav-search').val();
		var type = $('#nav-search-type').val();
		var formData = {ajax_action:"search_navbar",query:query,type:type};
		$.ajax({
			url : window.base_url+"/ajax_handler.php",
			type: "POST",
			data : formData,
			dataType:"json",
			success: function(data){
				var item;
				$('.nav-search-typeahead').typeahead('destroy');
				if(type == 'Projects'){
					item = '<li><a class="dropdown-item" href="#" onclick="navbar_search_project(this)" role="option"></a></li>';
				}else if(type == 'Users'){
					item = '<li><a class="dropdown-item" onclick="navbar_search_user(this)" href="#" role="option"></a></li>';
				}else{
					item = '<li><a class="dropdown-item" onclick="navbar_search_library(this)" href="#" role="option"></a></li>';
				}
				$(".nav-search-typeahead").typeahead({source:data, autoSelect: true, delay:200, item:item});
			}
		});
    });
	
	$('#typeahead-input').on('keyup', function () {
		var query = $('#typeahead-input').val();
		var formData = {ajax_action:"select_users_like",username:query};
		$.ajax({
			url : window.base_url+"/ajax_handler.php",
			type: "POST",
			data : formData,
			dataType:"json",
			success: function(data){
				$('.add-editors-typeahead').typeahead('destroy');
				$(".add-editors-typeahead").typeahead({source:data, autoSelect: true, delay:200});
			}
		});
    });
	
	
	$('#filter_libs_input').on('keyup', function () {
		var query = $('#filter_libs_input').val();
		var formData = {ajax_action:"filter_libraries",query:query};
		$.ajax({
			url : window.base_url+"/ajax_handler.php",
			type: "POST",
			data : formData,
			success: function(data){
				$("#libraries_container").html(data);
			}
		});
    });
	
	$('#filter_users_input').on('keyup', function () {
		var query = $('#filter_users_input').val();
		var formData = {ajax_action:"filter_users",query:query};
		$.ajax({
			url : window.base_url+"/ajax_handler.php",
			type: "POST",
			data : formData,
			success: function(data){
				$("#users_container").html(data);
			}
		});
    });
	$('#filter_components_input').on('keyup', function () {
		var query = $('#filter_components_input').val();
		var formData = {ajax_action:"filter_components",query:query};
		$.ajax({
			url : window.base_url+"/ajax_handler.php",
			type: "POST",
			data : formData,
			success: function(data){
				$("#components_container").html(data);
			}
		});
    });
	$('#filter_projects_input').on('keyup', function () {
		var query = $('#filter_projects_input').val();
		var formData = {ajax_action:"filter_projects",query:query};
		$.ajax({
			url : window.base_url+"/ajax_handler.php",
			type: "POST",
			data : formData,
			success: function(data){
				$("#projects_container").html(data);
			}
		});
    });
	
	$('.collapse')
		.on('shown.bs.collapse', function(){
			$(this).parent().find(".glyphicon-chevron-down").removeClass("glyphicon-chevron-down").addClass("glyphicon glyphicon-chevron-up");
		})
		.on('hidden.bs.collapse', function(){
			$(this).parent().find(".glyphicon-chevron-up").removeClass("glyphicon-chevron-up").addClass("glyphicon-chevron-down");
		});
	
});

function navbar_search_project(e) {
	var query = $(e).html();
	var formData = {ajax_action:"select_project_by_name",name:query};
	$.ajax({
		url : window.base_url+"/ajax_handler.php",
		type: "POST",
		data : formData,
		dataType:"json",
		success: function(data){
			var link = window.location.protocol + "//" + window.location.host + "/vhdl/project/"+data['owner']+"/"+data['project'];
			window.location.replace(link);
		}
	});
};

function navbar_search_user(e) {
	var user = $(e).html();
	var link = window.location.protocol + "//" + window.location.host + "/vhdl/project/"+user;
	window.location.replace(link);
};

function navbar_search_library(e) {
	var library = $(e).html();
	var link = window.location.protocol + "//" + window.location.host + "/vhdl/libraries/"+library;
	window.location.replace(link);
};

function typeahead_update_value(e) {
	var item = $(e).html();
	$('#editor-users').append("<li class='list-group-item'><span class='editor-item'>"+item+"</span><span onclick='typeahead_remove_item(this)' class='glyphicon glyphicon-remove pull-right btn btn-danger btn-xs' aria-hidden='true'></span></li>");
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

function lib_change_page(page) {
	var form = $("#filter_libraries");
	var link = window.location.protocol + "//" + window.location.host + "/vhdl/libraries/page/"+page;
	form.attr("action", link);
	form.attr("onsubmit", '');
	form.submit();
};

function admin_users_change_page(page) {
	var form = $("#filter_users");
	var link = window.location.protocol + "//" + window.location.host + "/vhdl/admin/users/page/"+page;
	form.attr("action", link);
	form.attr("onsubmit", '');
	form.submit();
};
function admin_components_change_page(page) {
	var form = $("#filter_components");
	var link = window.location.protocol + "//" + window.location.host + "/vhdl/admin/components/page/"+page;
	form.attr("action", link);
	form.attr("onsubmit", '');
	form.submit();
};
function admin_projects_change_page(page) {
	var form = $("#filter_projects");
	var link = window.location.protocol + "//" + window.location.host + "/vhdl/admin/projects/page/"+page;
	form.attr("action", link);
	form.attr("onsubmit", '');
	form.submit();
};

function confirm_user_removal_admin() {
	if( !confirm("Are you sure that you want to remove the user and all their projects ? ") ){
		event.preventDefault();
	}
};
function confirm_component_removal_admin() {
	if( !confirm("Are you sure that you want to remove the component ? ") ){
		event.preventDefault();
	}
};
function confirm_project_removal_admin() {
	if( !confirm("Are you sure that you want to remove the project and all it's files ? ") ){
		event.preventDefault();
	}
};