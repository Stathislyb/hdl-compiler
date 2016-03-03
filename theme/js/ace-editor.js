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
			$('#edit_status').html(returned);
			$('#edit_status').show();
			setTimeout(function() {
				$('#edit_status').fadeOut('fast');
			}, 7000); // 7 secs
		});
	}
});
	
});