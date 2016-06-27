<div id="waveforms_shadow" class="waveforms_shadow hidden"></div>
<div id="vcd_selection_container" class="row">
	<div class="col-sm-9 <?php echo (isset($project['id']) ? 'col-sm-offset-3':'' ); ?>">
		<h3>Display Waveforms</h3>
		<br/>
		<div class="row">
			<div class="col-sm-4">
				<label >Select VCD file:</label>
				<br/>
				<div class="form-group space-top-10">
					<select name="vcd_file" id="vcd_file" class="form-control width-auto">
					<?php
						foreach($vcd_files as $vcd) {
							$vcd_name = end(explode('/',$vcd));
							echo "<option value='".$vcd_name."'>".$vcd_name."</option>";
						}
					?>
					</select>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="form-group col-sm-4 space-top-10">
				<?php if(isset($project['id'])){ ?>
					<input type="hidden" value="<?php echo $project['id']; ?>" name="project_id" id="project_id">
					<button type="submit" class="btn btn-info full-row" id="Display_Waveform">Display Waveform</button>
				<?php }else{ ?>
					<button type="submit" class="btn btn-info full-row" id="SID_Display_Waveform">Display Waveform</button>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
	
<div id="waveforms_container" class="waveforms_container row hidden">
	<span id="waveforms_close_btn" class="glyphicon glyphicon-remove btn-close pointer"></span>
	<div class="col-lg-3">
		<div class="row">
			<h3>List of Signals</h3>
			<div id="signals" class="col-scroll-400"></div>
		</div>
		<div class="row">
			<h3>Simulator Tools</h3>
			<div class="row">
				<div class="col-sm-12">
					Time Scale :<br/>
					<div class="slider_main" id="slider_main"><div class="slider_button" id="slider_button"></div><input type="hidden" id="simulation_zoom" value="100"/></div><div class="slider_info" id="slider_info">100%</div>
					<br/>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					Data Type :<br/>
					<select class="form-control width-auto" id="waveform_data_type">
						<option value="1">Binary</option>
						<option value="2">Hexadecimal</option>
						<option value="3">Decimal</option>
					</select>
					<br/>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
				<a href="#" class="btn btn-success" id="btn-download-image" download="waveforms.png">Get Image</a>
				</div>
				<div class="col-sm-4">
				<a href="#" class="btn btn-info" id="btn-sim-tutorial">Tutorial</a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-9">
		<div class="row">
			<h3>Interactive Canvas</h3>
			<div id="canvas_div" class="minh-50">
				<canvas id="WavesCanvas" width="800" height="1" class="pointer">
					Your browser does not support the HTML5 canvas tag.</canvas>
				<canvas id="BufferCanvas">
					Your browser does not support the HTML5 canvas tag.</canvas>
			</div>
		</div>
		<div class="row hidden_soft" id="waveforms_viewer_tutorial">
			<h3>Tutorial</h3>
			<div class="col-sm-12">
				<p>
				<h4>Adding / Removing Signals from the Viewer :</h4>
				1. Click on the desired signals from the "List of Signals" to select and display them on the waveform viewer.<br/>
				2. You may click on a selected signal to remove it from the viewer.<br/>
				</p><br/><p>
				<h4>Viewing Detailed information :</h4>
				1. Move the mouse over the signals' waveforms to get detailed information on the cursor's position for each wave.<br/>
				2. You may left click on the waveforms to leave a more permanent marker on the desired position.<br/>
				3. Signals with multiple values can be expanded by clicking the plus ("+") symbol next to their name. <br/>
				4. Expanded signals can then be retracted by clicking the minus ("-") symbol next to their name.<br/>
				5. The displayed values can change between Binary, Hexadecimal and Decimal through the Data Type menu in the Simulator Tools.<br/>
				</p><br/><p>
				<h4>Time scaling (zoom in/out) :</h4>
				1. Adjust the time scale through the Time Scale slider in the Simulator Tools. The default value displays 100% of the waveforms.<br/>
				2. If the time scale is less than 100%, you may scroll through the waveform by holding down the left or right mouse button and dragging the waves to the left or to the right.<br/>
				</p><br/><p>
				<h4>Download Image from the waveform :</h4>
				1. Click on Get Image from the Simulator Tools to download an image of the waveform viewer.<br/>
				2. If you have set a permanent marker on the waveform, it will be contained on the image.<br/>
				</p><br/><p>
				<i>You may click on Tutorial in the Simulator Tools to hide/show these information.</i>
				</p>
			</div>
		</div>
	</div>
</div>
	
	<script src="<?php echo $BASE_URL; ?>/theme/js/waveforms_viewer.js" type="text/javascript" charset="utf-8"></script>