$(function() { 
	window.wave_intervals;
	window.wave_data;
	window.selected_signals = [];
	window.low_limit=0;
	window.max_limit=30;
	
	var isDown = false;
	var delay_pos=0;
	var	delay_neg=0;
	var mouse_down_x;
	var mouse_down_y;
	
	
	$("#btn-download-image").click(function(event){
		var canvas = document.getElementById("WavesCanvas");
		var dataURL = canvas.toDataURL('image/png');
		var img = canvas.toDataURL("image/jpg");
		$("#btn-download-image").attr('href', dataURL);
	});
	
	$("#btn-sim-tutorial").click(function(event){
		$("#waveforms_viewer_tutorial").toggle();
	});
	
	$("#waveforms_close_btn").click(function(event){
		$("#waveforms_container").addClass("hidden");
		$("#waveforms_shadow").addClass("hidden");
		$("#signals").html('');
		var canvas=document.getElementById("WavesCanvas");
		canvas.height = 0;
		window.selected_signals = [];
		window.low_limit=0;
		window.max_limit=30;
		$("#slider_button").css('left', '100px');
		$("#simulation_zoom").val('100');
		$("#slider_info").html('100%');
		$( ".expand_button").remove();
	});
	
	$("#Display_Waveform").click(function(event){
		var vcd_name = $("#vcd_file").val();
		var project_id = $("#project_id").val();
		var data = {ajax_action:"read_vcd", vcd_name:vcd_name, project_id:project_id};
		$.ajax({
			type: "POST",
			url: window.base_url+"/ajax_handler.php",
			data: data,
			dataType: "json",
			success: function(data){  
				var modules_array;
				var module ;
				var counter=0;
				if(data['time_info']['intervals'] < 30){
					max_limit = data['time_info']['intervals'];
				}else{
					max_limit=30;
				}
				wave_intervals = data['time_info']['intervals'];
				wave_data = data;
				
				$("#signals").append("<ul id='module_none' class='list-group'></ul>");
				$.each(data, function( index, signal ) {
					if(signal['name'] && index != "time_info"){

						if(signal["module"]){
							modules_array = signal["module"].split("_-_")
							module = create_modules(modules_array);
						}else{
							module="none";
						}
						var signal_html = "<li id='signal_"+counter+"' class='list-group-item pointer'>"+signal['name'];
						signal_html += "<span id='signal_"+counter+"_glyph' class='glyphicon right'></span>";
						if(index=="'"){
							signal_html += "<input type='hidden' id='signal_"+counter+"_val' value=\"'\" /></li>";
						}else{
							signal_html += "<input type='hidden' id='signal_"+counter+"_val' value='"+index+"' /></li>";
						}
						$("#module_"+module).append(signal_html);
						$("#signal_"+counter).click(function(event) {
							var signal_symbol = $("#"+event.target.id+"_val").val();
							var signal_index=window.selected_signals.indexOf(signal_symbol);
							$("#"+event.target.id).toggleClass('selected_sig');
							$("#"+event.target.id+"_glyph").toggleClass('glyphicon-ok');
							if(signal_index == -1){
								window.selected_signals[window.selected_signals.length] = signal_symbol;
								draw_wave(data);
							}else{
								window.selected_signals.splice(signal_index, 1);
								window.wave_data[index]["expand"]=0;
								$( ".expand_button").remove();
								draw_wave(data);
							}
						});
						counter++;
					}
				}); // end for each
			} // end success
		}); // end ajax
		$("#waveforms_container").removeClass("hidden");
		$("#waveforms_shadow").removeClass("hidden");
		$('html, body').animate({ scrollTop: 0});
		$(window).scrollTop();
	});
	
	$("#SID_Display_Waveform").click(function(event){
		var vcd_name = $("#vcd_file").val();
		var data = {ajax_action:"read_vcd_sid", vcd_name:vcd_name};
		$.ajax({
			type: "POST",
			url: window.base_url+"/ajax_handler.php",
			data: data,
			dataType: "json",
			success: function(data){  
				var modules_array;
				var module ;
				var counter=0;
				if(data['time_info']['intervals'] < 30){
					max_limit = data['time_info']['intervals'];
				}else{
					max_limit=30;
				}
				wave_intervals = data['time_info']['intervals'];
				wave_data = data;
				
				$("#signals").append("<ul id='module_none' class='list-group'></ul>");
				$.each(data, function( index, signal ) {
					if(signal['name'] && index != "time_info"){

						if(signal["module"]){
							modules_array = signal["module"].split("_-_")
							module = create_modules(modules_array);
						}else{
							module="none";
						}
						var signal_html = "<li id='signal_"+counter+"' class='list-group-item pointer'>"+signal['name'];
						signal_html += "<span id='signal_"+counter+"_glyph' class='glyphicon right'></span>";
						if(index=="'"){
							signal_html += "<input type='hidden' id='signal_"+counter+"_val' value=\"'\" /></li>";
						}else{
							signal_html += "<input type='hidden' id='signal_"+counter+"_val' value='"+index+"' /></li>";
						}
						$("#module_"+module).append(signal_html);
						$("#signal_"+counter).click(function(event) {
							var signal_symbol = $("#"+event.target.id+"_val").val();
							var signal_index=window.selected_signals.indexOf(signal_symbol);
							$("#"+event.target.id).toggleClass('selected_sig');
							$("#"+event.target.id+"_glyph").toggleClass('glyphicon-ok');
							if(signal_index == -1){
								window.selected_signals[window.selected_signals.length] = signal_symbol;
								draw_wave(data);
							}else{
								window.selected_signals.splice(signal_index, 1);
								window.wave_data[index]["expand"]=0;
								$( ".expand_button").remove();
								draw_wave(data);
							}
						});
						counter++;
					}
				}); // end for each
			} // end success
		}); // end ajax
		$("#waveforms_container").removeClass("hidden");
		$("#waveforms_shadow").removeClass("hidden");
		$('html, body').animate({ scrollTop: 0});
		$(window).scrollTop();
	});
	
	// prevent default right click behaviour in canvas
	$("canvas").on("contextmenu",function(){
       return false;
    }); 
	
	$("#WavesCanvas").mousedown(function(event){
		x=event.pageX - $('#WavesCanvas').offset().left;
		canvas_width = $('#WavesCanvas').width();
		draw_wave(wave_data);
		if(x > 110 && x < (canvas_width-20)){
			var second_divisions = ["ds", "cs", "ms", "µs", "ns", "ps", "fs", "as", "zs", "yz"];
			var second_div_index=second_divisions.indexOf(wave_data['time_info']['timescale']);
			x_interval =(canvas_width-130) / (max_limit - low_limit) ;
			time_interval = wave_data['time_info']['duration'] / ( wave_data['time_info']['intervals'] -1);
			timeframe =( ( Math.floor((x-110) / x_interval) + low_limit ) * time_interval );
			sub=scale_time_subdivisions(time_interval,0);
			timeframe_str = timeframe / (Math.pow(1000, sub));
			
			
			var wave_val=0;
			var current_time_temp=0;
			
			// get the canvas element
			var canvas = document.getElementById("WavesCanvas");
			var h = canvas.height;
			var buffer = document.getElementById("BufferCanvas");
			
			// start from (15,36)
			var x_canvas=15;
			var y_canvas=36;
			
			// init. the drawing colours and fonts
			var ctx=buffer.getContext("2d");
			// init. stroke and fill colors : black
			ctx.fillStyle = "#000";
			ctx.strokeStyle = "#000";
			// init. font style : Times New Roman, size : 12px
			ctx.font="12px 'Times New Roman'";
			
			$.each(window.selected_signals, function( i, index ) {
				for(current_time=0;current_time<=timeframe;current_time+=time_interval){
					if(typeof wave_data[index][current_time] != 'undefined'){
						wave_val = wave_data[index][current_time];
						if(wave_data[index]['length']>1){
							wave_val_bin = wave_val;
							data_type=$("#waveform_data_type").val();
							if(wave_val.match(/bU.*/g)){
								wave_val=wave_val.substr(1);
							}else{
								if(data_type=='3'){
									wave_val="D"+parseInt(wave_val.substr(1),2).toString(10);
								}else if(data_type=='2'){
									wave_val="H"+parseInt(wave_val.substr(1),2).toString(16);
								}else{
									wave_val="B"+wave_val.substr(1);
								}
							}
						}
					}	
				}
				
				ctx.fillText("Value : "+wave_val,x_canvas,y_canvas);
				y_canvas=y_canvas+40;
				if(wave_data[index]["expand"]==1){
					for(j=1;j<=wave_data[index]["length"];j++){
						sub_wave_val=wave_val_bin.charAt(j);
						ctx.fillText("Value : "+sub_wave_val,x_canvas+15,y_canvas);
						y_canvas=y_canvas+40;
					}
				}
			});
			
			ctx.beginPath();
			ctx.strokeStyle = "#4af";
			ctx.moveTo(x,0);
			ctx.lineTo(x,h-13);
			ctx.stroke();
			ctx.closePath();
			x_time=x- ((timeframe_str.toString().length / 2) +1)*5;
			fill_text = timeframe_str.toString()+second_divisions[second_div_index-sub];
			ctx.fillStyle = "#fff";
			ctx.fillRect(x_time-2,h-12,fill_text.length*7,12);
			ctx.fillStyle = "#000";
			ctx.fillText(fill_text,x_time,h-2);
			
			var ctx_canvas=canvas.getContext("2d");
			ctx_canvas.drawImage(buffer, 0,0);
			
		}
		
		isDown = true;
		delay_pos=0;
		delay_neg=0;
		mouse_down_x= event.pageX;
		
	});
	$(document).mouseup(function(event){
		if(isDown){
			isDown = false;
			delay_pos=0;
			delay_neg=0;
		}
	});
	
	
	$( "#WavesCanvas" ).mousemove(function( event ) {
		if(isDown){
			var mouse_x= event.pageX;
			var dif = mouse_down_x-mouse_x;
			if(dif>0){
				delay_pos = dif/10 + delay_pos;
				if(delay_pos>=1){
					delay_pos=0;
					dif=1;
				}else{
					dif=0;
				}
			}
			if(dif<0){
				delay_neg = dif/10 + delay_neg;
				if(delay_neg<=-1){
					delay_neg=0;
					dif=-1;
				}else{
					dif=0;
				}
			}
			
			mouse_down_x = mouse_x;
			temp_low_limit = low_limit +dif;
			temp_max_limit = max_limit +dif;
			if(temp_low_limit < 0){
				low_limit=0;
			}else{
				if(temp_max_limit > wave_intervals){
					low_limit = wave_intervals - (max_limit - low_limit);
					max_limit = wave_intervals;
				}else{
					low_limit = temp_low_limit;
					max_limit = temp_max_limit;
				}
			}
			
			if(dif!=0){
				draw_wave(wave_data);
			}
		}
		canvas_width = $('#WavesCanvas').width();
		x=event.pageX - $('#WavesCanvas').offset().left ;	
		
		if(x > 110 && x < (canvas_width-20)){
			var second_divisions = ["ds", "cs", "ms", "µs", "ns", "ps", "fs", "as", "zs", "yz"];
			var second_div_index=second_divisions.indexOf(wave_data['time_info']['timescale']);
			x_interval =(canvas_width-130) / (max_limit - low_limit) ;
			time_interval = wave_data['time_info']['duration'] / ( wave_data['time_info']['intervals'] -1);
			timeframe =( ( Math.floor((x-110) / x_interval) + low_limit ) * time_interval );
			sub=scale_time_subdivisions(time_interval,0);
			timeframe_str = timeframe / (Math.pow(1000, sub));
			
			var wave_val=0;
			var current_time_temp=0;
			
			// get the canvas element
			var canvas=document.getElementById("WavesCanvas");
			var h = canvas.height;
			var buffer = document.getElementById("BufferCanvas");
			
			// start from (15,36)
			var x_canvas=15;
			var y_canvas=36;
			
			// init. the drawing colours and fonts
			var ctx=canvas.getContext("2d");
			ctx.drawImage(buffer, 0,0);
			// init. stroke and fill colors : black
			ctx.fillStyle = "#000";
			ctx.strokeStyle = "#000";
			// init. font style : Times New Roman, size : 12px
			ctx.font="12px 'Times New Roman'";
			
	
			$.each(window.selected_signals, function( i, index ) {
					
				for(current_time=0;current_time<=timeframe;current_time+=time_interval){
					if(typeof wave_data[index][current_time] != 'undefined'){
						wave_val = wave_data[index][current_time];
						if(wave_data[index]['length']>1){
							wave_val_bin = wave_val;
							data_type=$("#waveform_data_type").val();
							if(wave_val.match(/bU.*/g)){
								wave_val=wave_val.substr(1);
							}else{
								if(data_type=='3'){
									wave_val="D"+parseInt(wave_val.substr(1),2).toString(10);
								}else if(data_type=='2'){
									wave_val="H"+parseInt(wave_val.substr(1),2).toString(16);
								}else{
									wave_val="B"+wave_val.substr(1);
								}
							}
						}
					}	
				}
				if(wave_val[wave_val.length-1]=='U'){
					digit_width=9;
				}else{
					digit_width=6;
				}
				ctx.fillStyle = "#ffa";
				ctx.fillRect(x_canvas,y_canvas-10,38+wave_val.length*digit_width,10);
				ctx.fillStyle = "#000";
				ctx.fillText("Value : "+wave_val,x_canvas,y_canvas);
				y_canvas=y_canvas+40;
				if(wave_data[index]["expand"]==1){
					for(j=1;j<=wave_data[index]["length"];j++){
						sub_wave_val=wave_val_bin.charAt(j);
						ctx.fillStyle = "#efb";
						ctx.fillRect(x_canvas+15,y_canvas-10,65,10);
						ctx.fillStyle = "#000";
						ctx.fillText("Value : "+sub_wave_val,x_canvas+15,y_canvas);
						y_canvas=y_canvas+40;
					}
				}
			});
			
			ctx.beginPath();
			ctx.strokeStyle = "#faa";
			ctx.moveTo(x,0);
			ctx.lineTo(x,h-13);
			ctx.stroke();
			ctx.closePath();
			x_time=x- ((timeframe_str.toString().length / 2) +1)*5;
			fill_text = timeframe_str.toString()+second_divisions[second_div_index-sub];
			ctx.fillStyle = "#fff";
			ctx.fillRect(x_time-2,h-12,fill_text.length*7,12);
			ctx.fillStyle = "#000";
			ctx.fillText(fill_text,x_time,h-2);
			
		}
	});
	
	
	// When the mouse leaves the canvas, redraw it from the buffer 
	$( "#WavesCanvas" ).mouseout(function( event ) {
		var canvas=document.getElementById("WavesCanvas");
		var buffer = document.getElementById("BufferCanvas");
		var ctx=canvas.getContext("2d");
		ctx.drawImage(buffer, 0,0);
	});
		
		
	// Slider's event listener
	$("#slider_button").mousedown(function(event){
		if(window.wave_data){
			$('body').on('mousemove', function(e) {
				// Calculate the slider's value
				offset=e.pageX - $('#slider_main').offset().left  - $('.slider_button').outerWidth()/2;
				offset = Math.round(offset);
				if(offset<0){ offset =1; }
				if(offset>100){ offset =100; }
				
				// move the slider and the info box
				$("#slider_button").css('left', offset+'px');
				
				// update the time scale value and redraw the data
				$("#simulation_zoom").val(offset);
				draw_wave(wave_data);
				
				// update the info value of the info box
				$("#slider_info").html(offset +'%');
				
				// prevent default behaviour of mouse drag
				e.preventDefault();
			}).on('mouseup', function() {
				$('body').unbind( "mousemove");
			});
		}
	});
	
	// Data type dropdown event listener
	$("#waveform_data_type").change(function(event){
		if(window.wave_data){
			draw_wave(wave_data);
		}
	});
	
});


function create_modules(modules_array){
	var module = modules_array.pop();
	var perent_element;
	if (module){
		if ( $("#module_"+module).length == 0){ 
			parent_module = create_modules(modules_array);
			if(parent_module == -1){
				perent_element = $("#signals");
			}else{
				perent_element = $("#module_"+parent_module);
			}
			var module_html = "<ul class='list-group fill_gap-5'><li id='span_module_"+module+"' class='list-group-item list-header bold pointer'><span id='module_"+module+"_glyph' class='glyphicon glyphicon-triangle-bottom'></span> "+module+"</li></ul>";
			module_html += "<ul id='module_"+module+"' class='list-group hidden'></ul>";
			perent_element.append(module_html);
			$( "#span_module_"+module ).click(function() {
				$( "#module_"+module ).toggleClass( "hidden" );
				$( "#module_"+module+"_glyph" ).toggleClass( "glyphicon-triangle-top" ).toggleClass("glyphicon-triangle-bottom");
			});
		}
		return module;
	}else{
		return -1;
	}
}


function draw_wave(signal_data){
	var time_interval=1;
	var x_multi_intervals=0;
	var first_timeframe=1;
	var time_frame = signal_data['time_info']['duration'] / (signal_data['time_info']['intervals']-1);
	var second_divisions = ["ds", "cs", "ms", "µs", "ns", "ps", "fs", "as", "zs", "yz"];
	var second_div_index=second_divisions.indexOf(signal_data['time_info']['timescale']);
	var sub=scale_time_subdivisions(time_frame,0);
	var second_unit=second_divisions[second_div_index-sub];
	max_limit = low_limit + Math.round( signal_data['time_info']['intervals']*($("#simulation_zoom").val()/100) );
	if(max_limit > signal_data['time_info']['intervals']){
		low_limit = low_limit - (max_limit - signal_data['time_info']['intervals']);
		max_limit = signal_data['time_info']['intervals'];
	}
	
	
	time_interval = max_limit - low_limit;
	if( time_interval > 9){
		time_interval=Math.round(time_interval/9);
	}else{
		time_interval=1;
	}
	
	// get the canvas element
	var canvas=document.getElementById("WavesCanvas");
	
	// get the canvas width	
	var w = $("#WavesCanvas").width();
	if(w > 800){
		canvas.width=800;
		w=800;
	}else{
		canvas.width=w;
	}
	
	// calculate the length of each pulse
	var x_interval=(w-130) / (max_limit - low_limit) ;
	
	// fix the canvas height to fit all the waves
	var y_interval = 40;
	canvas.height = y_interval*window.selected_signals.length+40;
	var h = canvas.height;
	var selected_signals_num = window.selected_signals.length;
	$.each(window.selected_signals, function( i, index ) {
		if(signal_data[index]["expand"] == 1){
			h+=y_interval*signal_data[index]["length"];
			selected_signals_num += parseInt(signal_data[index]["length"], 10);
		}
	});
	canvas.height = h;
	// start from (5,20)
	var x=15;
	var y=20;
	
	// init. the drawing colours and fonts
	var ctx=canvas.getContext("2d");
	// fill the canvas with color : white
	ctx.fillStyle = "#fff";
	ctx.fillRect(0,0,w,h);
	// init. stroke and fill colors : black
	ctx.fillStyle = "#000";
	ctx.strokeStyle = "#000";
	// init. font style : Times New Roman, size : 12px
	ctx.font="12px 'Times New Roman'";
	// draw the grid
	draw_grid(x,y,h,signal_data['time_info']['duration'],time_frame,low_limit,max_limit,x_interval,ctx,sub,second_unit, selected_signals_num);
	
	$.each(window.selected_signals, function( i, index ) {
		if(signal_data[String(index)]['length']>1){
			if($('#expand_'+i).length == 0){
				corrected_y=y+42;
				$('#canvas_div').append('<div id="expand_'+i+'" class="expand_button" style="top: '+corrected_y+'px;">+</div>');
				$("#expand_"+i).click(function(event) {
					if(window.wave_data[index]["expand"]==1){
						window.wave_data[index]["expand"]=0;
						draw_wave(window.wave_data);
						$(this).html('+');
					}else{
						window.wave_data[index]["expand"]=1;
						draw_wave(window.wave_data);
						$(this).html('-');
					}
				});
			}else{
				corrected_y=y+42;
				$('#expand_'+i).css('top', corrected_y + 'px');
			}
		}
		
		// draw the names of the waveforms
		ctx.moveTo(x,y);
		ctx.fillText(signal_data[index]['name'],15,y);
		
		// move through x for the waveform
		x=110;
		
		// draw the wave form
		draw_signal(x,y,signal_data[index],signal_data['time_info']['duration'],time_frame,low_limit,max_limit,x_interval,ctx,sub,second_unit,time_interval);
		
		if(signal_data[index]["expand"] == 1){
			expanded_y = y_interval*signal_data[index]["length"];
			h=h+expanded_y;
			ctx.beginPath();
			ctx.strokeStyle = "#8a8";
			//small vertical
			ctx.moveTo(5,y+5);
			ctx.lineTo(5,y+25);
			//small horizontal
			ctx.moveTo(5,y+25);
			ctx.lineTo(15,y+25);
			//vertical for the rest of the length
			ctx.moveTo(15,y+25);
			ctx.lineTo(15,y+expanded_y+15);
			//small horizontal for close
			ctx.moveTo(15,y+expanded_y+15);
			ctx.lineTo(20,y+expanded_y+15);
			ctx.stroke();
			ctx.closePath();
			for(j=1;j<=signal_data[index]["length"];j++){
				y=y+40;
				sub_signal = jQuery.extend(true, {}, signal_data[index]);				
				$.each(sub_signal, function( id, value ) {
					if($.isNumeric( id )){
						sub_signal[id]=signal_data[index][id].charAt(j);
					}
				});
				
				sub_signal["length"]=1;
				// draw the names of the sub waveforms
				counter=j-1;
				ctx.fillText(sub_signal['name']+"["+counter+"]",25,y);
				draw_signal(x,y,sub_signal,signal_data['time_info']['duration'],time_frame,low_limit,max_limit,x_interval,ctx,sub,second_unit,time_interval);
			}
		}
		
		
		// reinit some values and move through the y axis
		y=y+40;
	});
		
	// Save canvas to buffer
	var buffer = document.getElementById("BufferCanvas");
	buffer.width = canvas.width;
	buffer.height = canvas.height;
	context = buffer.getContext('2d');	
	context.drawImage(canvas, 0,0);
}


function draw_signal(x,y,signal_data,duration,time_frame,low_limit,max_limit,x_interval,ctx,sub,second_unit,time_interval){
	var pulse_height=10;
	var old_pulse_height=-1;
	var old_multivalue_named=-1;
	var old_value =-1;
	var old_pulse_height=-1;
	var first_timeframe=1;
	var x_multi_intervals=0;
	var last_frame=1;
	var time_offset_y=15;
	var time_counter=0;
	var canvas=document.getElementById("WavesCanvas");
	var h = canvas.height;
	
	//  for each time frame in the signal
	for(current_time=0;current_time<=duration;current_time+=time_frame){
		if(typeof signal_data[current_time] != 'undefined'){
			old_value = signal_data[current_time];
			value = signal_data[current_time];
			
		}else{
			value=-1;
		}
		if(!isNaN(current_time) && current_time >= low_limit*time_frame && current_time < max_limit*time_frame){
			
			// begin path for the pulse
			ctx.beginPath();
			// default pulse color : blue
			ctx.strokeStyle = "#015";	
			
			if(signal_data['length']==1){
				if(value == -1){
					value = old_value;
				}
				
				// fix the pulse height depending the value
				if (value==1){
					pulse_height=-10;
				}else if(value==0){
					pulse_height=10;
				}else if(value=='U'){
					// unknown value pulse height : 0, color : red 
					pulse_height =0;
					ctx.strokeStyle = "#f00";
				}
				
				if(old_pulse_height == -1){
					old_pulse_height = pulse_height;
				}
				
				// create the horizontal pulse for this time frame 
				ctx.moveTo(x, y+ pulse_height);
				ctx.lineTo(x+x_interval, y+ pulse_height);
				// create the vertical pulse for this time frame
				ctx.moveTo(x, y+ old_pulse_height);
				ctx.lineTo(x, y+ pulse_height);
				
			}else{
				if(value == -1){
					value = old_value;
					x_multi_intervals = x_interval + x_multi_intervals;
				}else{
					if(first_timeframe != 1){
						
						data_type=$("#waveform_data_type").val();
						if(old_multivalue_named.match(/bU.*/g)){
							value_length=old_multivalue_named.length*10;
							data_val=old_multivalue_named.substr(1);
						}else{
							if(data_type=='3'){
								data_val="D"+parseInt(old_multivalue_named.substr(1),2).toString(10);
							}else if(data_type=='2'){
								data_val="H"+parseInt(old_multivalue_named.substr(1),2).toString(16);
							}else{
								data_val="B"+old_multivalue_named.substr(1);
							}
							value_length=data_val.length*8;
						}
						if(value_length < x_multi_intervals){
							ctx.fillText(data_val,x-x_multi_intervals+5,y+5);
						}							
					}
					x_multi_intervals=x_interval;
					ctx.moveTo(x, y+10);
					ctx.lineTo(x, y-10);
					ctx.moveTo(x+1.5, y-1.5);
					ctx.arc(x, y, 3, 0, 2 * Math.PI, false);
				}
				
				if( old_value.match(/bU.*/g)){
					ctx.strokeStyle = "#f00";
				}
				
				ctx.moveTo(x, y+10);
				ctx.lineTo(x+x_interval, y+10);
				ctx.moveTo(x, y-10);
				ctx.lineTo(x+x_interval, y-10);
			}
			
			x=x+x_interval;
			old_pulse_height=pulse_height;
			
			ctx.stroke();
			ctx.closePath();
			first_timeframe=0;
		}
		
		
		if((current_time >= max_limit*time_frame || current_time == duration) && last_frame==1){
			last_frame=0;
			if(signal_data['length']>1){
				data_type=$("#waveform_data_type").val();
				if(old_multivalue_named.match(/bU.*/g)){
					value_length=old_multivalue_named.length*10;
					data_val=old_multivalue_named.substr(1);
				}else{
					if(data_type=='3'){
						data_val="D"+parseInt(old_multivalue_named.substr(1),2).toString(10);
					}else if(data_type=='2'){
						data_val="H"+parseInt(old_multivalue_named.substr(1),2).toString(16);
					}else{
						data_val="B"+old_multivalue_named.substr(1);
					}
					value_length=data_val.length*8;
				}
				if(value_length < x_multi_intervals){
					ctx.fillText(data_val,x-x_multi_intervals+5,y+5);
				}
			}
			
			if(time_offset_y == 15){
				time_offset_y = 0;
			}else{
				time_offset_y=15;
			}
			
		}
		
		old_multivalue_named=old_value;
	}
	
}

function draw_grid(x,y,h,duration,time_frame,low_limit,max_limit,x_interval,ctx,sub,second_unit,selected_signals_num){
	var canvas=document.getElementById("WavesCanvas");
	var w = canvas.width;
	x=110;
	wave_border = 110 +(max_limit-low_limit)*x_interval;
	// draw 0 state for each signal
	for(signals=0;signals<selected_signals_num;signals++){
		ctx.beginPath();
		ctx.strokeStyle = "#ddd";
		ctx.moveTo(x,y);
		ctx.lineTo(wave_border,y);
		ctx.stroke();
		ctx.closePath();
		y+=40;
	}
	// draw timeline base horizontal
	ctx.beginPath();
	ctx.strokeStyle = "#444";
	ctx.moveTo(x,h-13);
	ctx.lineTo(wave_border,h-13);
	ctx.stroke();
	ctx.closePath();
	// draw timeline base vertical
	ctx.beginPath();
	ctx.strokeStyle = "#222";
	ctx.moveTo(x,0);
	ctx.lineTo(x,h-13);
	ctx.stroke();
	ctx.closePath();
	//  for each time frame
	for(current_time=0;current_time<=duration;current_time+=time_frame){
		if(!isNaN(current_time) && current_time >= low_limit*time_frame && current_time < max_limit*time_frame){
			
			//draw timeline grid
			cur_time_normalized = current_time / (Math.pow(1000, sub));
			time_frame_normalized = time_frame / (Math.pow(1000, sub));
			division_1 = 10;
			division_2 = 5;
			division_3 = 1;
			if( (cur_time_normalized%division_1 ) == 0){
				ctx.beginPath();
				ctx.strokeStyle = "#cdcdcd";
				ctx.moveTo(x,0);
				ctx.lineTo(x,h-13);
				ctx.stroke();
				ctx.closePath();
				ctx.beginPath();
				ctx.strokeStyle = "#444";
				ctx.moveTo(x,h-13);
				ctx.lineTo(x,h-23);
				ctx.stroke();
				ctx.closePath();
				current_time_str = ( cur_time_normalized ).toString();
				x_time=x- ((current_time_str.length / 2) +1)*5;
				ctx.fillText(current_time_str+second_unit,x_time,h-2);
			}else if( (cur_time_normalized%division_2) == 0){
				ctx.beginPath();
				ctx.strokeStyle = "#dadada";
				ctx.moveTo(x,0);
				ctx.lineTo(x,h-13);
				ctx.stroke();
				ctx.closePath();
				ctx.beginPath();
				ctx.strokeStyle = "#444";
				ctx.moveTo(x,h-13);
				ctx.lineTo(x,h-18);
				ctx.stroke();
				ctx.closePath();
				if( ((max_limit-low_limit)*time_frame_normalized) < 100){
					current_time_str = ( cur_time_normalized ).toString();
					x_time=x- ((current_time_str.length / 2) +1)*5;
					ctx.fillText(current_time_str+second_unit,x_time,h-2);
				}
			}else if( (cur_time_normalized%division_3) == 0){
				ctx.beginPath();
				ctx.strokeStyle = "#eee";
				ctx.moveTo(x,0);
				ctx.lineTo(x,h-13);
				ctx.stroke();
				ctx.closePath();
				ctx.beginPath();
				ctx.strokeStyle = "#444";
				ctx.moveTo(x,h-13);
				ctx.lineTo(x,h-15);
				ctx.stroke();
				ctx.closePath();
				if( ((max_limit-low_limit)*time_frame_normalized) < 21){
					current_time_str = ( cur_time_normalized ).toString();
					x_time=x- ((current_time_str.length / 2) +1)*5;
					ctx.fillText(current_time_str+second_unit,x_time,h-2);
				}
			}
			x=x+x_interval;
		}
	}
}

function scale_time_subdivisions(input,sub){
	input=input/1000;
	sub++;
	if( input % 10 == 0){
		sub=scale_time_subdivisions(input,sub);
	}
	return sub;
}