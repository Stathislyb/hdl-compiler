<div id="vcd_selection_container" class="row">
	<div class="col-sm-9 col-sm-offset-3">
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
				<?php if(){} ?>
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
	<div id="signals" class="col-lg-3 col-scroll-400">
	</div>
	<div id="canvas_div" class="col-lg-9">
		<canvas id="WavesCanvas" width="800" height="1" class="pointer">
			Your browser does not support the HTML5 canvas tag.</canvas>
		<canvas id="BufferCanvas">
			Your browser does not support the HTML5 canvas tag.</canvas>
		<div id="WaveImage"></div>
		Time Scale (default 100%) : <div class="slider_main" id="slider_main"><div class="slider_button" id="slider_button"></div><input type="hidden" id="simulation_zoom" value="100"/></div><div class="slider_info" id="slider_info">100%</div>
		</div>
</div>
	
	<script src="<?php echo $BASE_URL; ?>/theme/js/waveforms_viewer.js" type="text/javascript" charset="utf-8"></script>