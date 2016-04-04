$(function() { 
	window.wave_intervals;
	window.wave_data;
	window.selected_signals = [];
	window.low_limit=0;
	window.max_limit=30;
	
	var isDown = false;
	var mouse_down_x;
	var mouse_down_y;
	
	$("#waveforms_close_btn").click(function(event){
		$("#waveforms_container").addClass("hidden");
		$("#signals").html('');
		$("#WaveImage").html('');
		var canvas=document.getElementById("WavesCanvas");
		var ctx=canvas.getContext("2d");
		ctx.clearRect(0, 0, canvas.width, canvas.height);
		window.selected_signals = [];
		window.low_limit=0;
		window.max_limit=30;
		$("#slider_button").css('left', '100px');
		$("#simulation_zoom").val('100');
		$("#slider_info").html('100%');
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
							var signal_index=selected_signals.indexOf(signal_symbol);
							$("#"+event.target.id).toggleClass('selected_sig');
							$("#"+event.target.id+"_glyph").toggleClass('glyphicon-ok');
							if(signal_index == -1){
								selected_signals[selected_signals.length] = signal_symbol;
								draw_wave(data);
							}else{
								selected_signals.splice(signal_index, 1);
								window.wave_data[index]["expand"]=0;
								$( "#expand_"+signal_index ).remove();
								draw_wave(data);
							}
						});
						counter++;
					}
				}); // end for each
			} // end success
		}); // end ajax
		$("#waveforms_container").removeClass("hidden");
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
							var signal_index=selected_signals.indexOf(signal_symbol);
							$("#"+event.target.id).toggleClass('selected_sig');
							$("#"+event.target.id+"_glyph").toggleClass('glyphicon-ok');
							if(signal_index == -1){
								selected_signals[selected_signals.length] = signal_symbol;
								draw_wave(data);
							}else{
								selected_signals.splice(signal_index, 1);
								window.wave_data[index]["expand"]=0;
								$( "#expand_"+signal_index ).remove();
								draw_wave(data);
							}
						});
						counter++;
					}
				}); // end for each
			} // end success
		}); // end ajax
		$("#waveforms_container").removeClass("hidden");
	});
	
	// prevent default right click behaviour in canvas
	$("canvas").on("contextmenu",function(){
       return false;
    }); 
	
	$("#WavesCanvas").mousedown(function(event){
		switch (event.which) {
		// for mouse 1 (left click), allow setting information marker 
		case 1: 
			draw_wave(wave_data);
			x=event.pageX - $('#WavesCanvas').offset().left;
			canvas_width = $('#WavesCanvas').width();
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
						}	
					}
					ctx.fillText("Value : "+wave_val,x_canvas,y_canvas);
					y_canvas=y_canvas+40;
					if(wave_data[index]["expand"]==1){
						for(j=1;j<=wave_data[index]["length"];j++){
							sub_wave_val=wave_val.charAt(j);
							ctx.fillText("Value : "+sub_wave_val,x_canvas+15,y_canvas);
							y_canvas=y_canvas+40;
						}
					}
				});
				
				ctx.beginPath();
				ctx.strokeStyle = "#4af";
				ctx.moveTo(x,0);
				ctx.lineTo(x,h-10);
				ctx.stroke();
				ctx.closePath();
				x_time=x- ((timeframe_str.toString().length / 2) +1)*5;
				ctx.fillText(timeframe_str.toString()+second_divisions[second_div_index-sub],x_time,h-5);
				
				var ctx_canvas=canvas.getContext("2d");
				ctx_canvas.drawImage(buffer, 0,0);
				
				// Recreate a jpg image of the canvas
				var img = canvas.toDataURL("image/jpg");
				document.getElementById("WaveImage").innerHTML='<img src="'+img+'"/>';
				
			}
		 
		
		// for mouse 2 (right click), allow time scrolling 
		case 3:
			isDown = true;
			mouse_down_x= event.pageX;
			mouse_down_y= event.pageY;
		} 
	});
	$(document).mouseup(function(event){
		if(isDown){
			isDown = false;
		}
	});
	
	
	$( "#WavesCanvas" ).mousemove(function( event ) {
		if(isDown){
			var mouse_up_x= event.pageX;
			var mouse_up_y= event.pageY;
			var dif = Math.round((mouse_down_x-mouse_up_x)/10);
			mouse_down_x = mouse_up_x;
			
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
			
			draw_wave(wave_data);
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
					}	
				}
				ctx.fillStyle = "#ffa";
				ctx.fillRect(x_canvas,y_canvas-10,80,10);
				ctx.fillStyle = "#000";
				ctx.fillText("Value : "+wave_val,x_canvas,y_canvas);
				y_canvas=y_canvas+40;
				if(wave_data[index]["expand"]==1){
					for(j=1;j<=wave_data[index]["length"];j++){
						sub_wave_val=wave_val.charAt(j);
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
			ctx.lineTo(x,h-10);
			ctx.stroke();
			ctx.closePath();
			x_time=x- ((timeframe_str.toString().length / 2) +1)*5;
			ctx.fillText(timeframe_str.toString()+second_divisions[second_div_index-sub],x_time,h-5);
			
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
	var x_interval = 95;
	var w = $("#WaveImage").width();
	if(w > 800){
		canvas.width=800;
		w=800;
	}else{
		canvas.width=w;
	}
	
	// fix the canvas height to fit all the waves
	var y_interval = 40;
	canvas.height = y_interval*window.selected_signals.length+40;
	var h = canvas.height;
	$.each(window.selected_signals, function( i, index ) {
		if(signal_data[index]["expand"] == 1){
			h+=y_interval*signal_data[index]["length"];
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
	
	$.each(window.selected_signals, function( i, index ) {
		
		if(signal_data[index]['length']>1){
			if($('#expand_'+i).length == 0){
				corrected_y=y-12;
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
				corrected_y=y-12;
				$('#expand_'+i).css('top', corrected_y + 'px');
			}
		}
		
		// draw the names of the waveforms
		ctx.moveTo(x,y);
		ctx.fillText(signal_data[index]['name'],15,y);
		
		// move through x for the waveform
		x=110;
		// calculate the length of each pulse
		x_interval=(w-130) / (max_limit - low_limit) ;
		
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
	
	// draw all the lines in the canvas
	
	// Create a jpg image of the canvas
	var img = canvas.toDataURL("image/jpg");
	document.getElementById("WaveImage").innerHTML='<img src="'+img+'"/>';
	
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
	var first_signal=0;
	var first_timeframe=1;
	var x_multi_intervals=0;
	var last_frame=1;
	var time_offset_y=15;
	var time_counter=0;
	
	//  for each time frame in the signal
	for(current_time=0;current_time<=duration;current_time+=time_frame){
		if(typeof signal_data[current_time] != 'undefined'){
			old_value = signal_data[current_time];
			value = signal_data[current_time];
			
		}else{
			value=-1;
		}
		if(!isNaN(current_time) && current_time >= low_limit*time_frame && current_time < max_limit*time_frame){
			
			ctx.strokeStyle = "#000";
			if(first_signal==1){
				if(time_counter==0){
					if(time_offset_y == 15){
						time_offset_y = 0;
					}else{
						time_offset_y=15;
					}
					ctx.beginPath();
					ctx.strokeStyle = "#aaa";
					ctx.moveTo(x,0);
					ctx.lineTo(x,h-40+time_offset_y);
					ctx.stroke();
					ctx.closePath();
					current_time_str = ( current_time / (Math.pow(1000, sub)) ).toString();
					x_time=x- ((current_time_str.length / 2) +1)*5;
					ctx.fillText(current_time_str+second_unit,x_time,h-30+time_offset_y);
					time_counter=time_interval;
				}else{
					time_counter--;
				}
				
			}
			
			// begin path for the pulse
			ctx.beginPath();
			// default pulse color : black
			ctx.strokeStyle = "#000";	
			
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
						if(old_multivalue_named.match(/bU.*/g)){
							value_length=old_multivalue_named.length*10;
						}else{
							value_length=old_multivalue_named.length*8;
						}
						if(value_length < x_multi_intervals){
							ctx.fillText(old_multivalue_named,x-x_multi_intervals+5,y+5);
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
				if(String(old_value).match(/bU.*/g)){
					value_length=old_value.length*10;
				}else{
					value_length=old_value.length*8;
				}
				if(value_length < x_multi_intervals){
					ctx.fillText(old_value,x-x_multi_intervals+5,y+5);
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

function scale_time_subdivisions(input,sub){
	input=input/1000;
	sub++;
	if( input % 1 == 0){
		sub=scale_time_subdivisions(input,sub);
	}
	return sub;
}