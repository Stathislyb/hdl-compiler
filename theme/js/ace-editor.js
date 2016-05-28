$( document ).ready(function() {

	$("#ace_save_button").click(function(){
		if( $('#editor').length ) {
			var editor = ace.edit("editor");
			var contents = editor.getSession().getValue();
			var directory = $('#path').val();
			var file_id = $('#file_id').val();
			var project_id = $('#project_id').val();
			var formData = {ajax_action:"save_file",directory: directory,data: contents, file_id: file_id, project_id:project_id};
			$.ajax({
				method: "POST",
				url: window.base_url+"/ajax_handler.php",
				data:formData
			}).done(function(returned) {
				var alert_html = '<div class="alert alert-success">';
				alert_html = alert_html+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
				alert_html = alert_html+returned+'</div>';
				$('#edit_status').html(alert_html);
			});
		}
	});
	
	$("#ace_save_button_library").click(function(){
		if( $('#editor').length ) {
			var editor = ace.edit("editor");
			var contents = editor.getSession().getValue();
			var directory = $('#path').val();
			var library_id = $('#library_id').val();
			var formData = {ajax_action:"save_library",directory: directory,data: contents, library_id: library_id};
			$.ajax({
				method: "POST",
				url: window.base_url+"/ajax_handler.php",
				data:formData
			}).done(function(returned) {
				var alert_html = '<div class="alert alert-success">';
				alert_html = alert_html+'<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
				alert_html = alert_html+returned+'</div>';
				$('#edit_status').html(alert_html);
			});
		}
	});
	
	$("#ace_theme").change(function(){
		if( $('#editor').length ) {
			var theme = $("#ace_theme option:selected").text().replace(/\s+/g, '_').toLowerCase();
			var theme_id = $("#ace_theme").val();
			var editor = ace.edit("editor");
			editor.setTheme("ace/theme/"+theme);
			var formData = {ajax_action:"save_theme",theme: theme_id};
			$.ajax({
				method: "POST",
				url: window.base_url+"/ajax_handler.php",
				data:formData
			});
		}
	});
});