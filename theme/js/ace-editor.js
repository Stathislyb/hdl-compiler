$( document ).ready(function() {

	$("#ace_save_button").click(function(){
		if( $('#editor').length ) {
			var editor = ace.edit("editor");
			var contents = editor.getSession().getValue();
			var directory = $('#path').val();
			$.ajax({
				method: "POST",
				url: "/writefile.php",
				data:{
					directory: directory,
					data: contents
				}
			}).done(function(returned) {
				var alert_html = '<div class="alert alert-success">';
				alert_html +='<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>';
				alert_html += returned+'</div>';
				$('#edit_status').html(alert_html);
			});
		}
	});
});