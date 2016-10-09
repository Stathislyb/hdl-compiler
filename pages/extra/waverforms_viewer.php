<div id="waveforms_shadow" class="waveforms_shadow hidden"></div>
<div id="vcd_selection_container" class="row">
	<div class="col-sm-9 <?php echo (isset($project['id']) ? 'col-sm-offset-3':'' ); ?>">
		<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_1'] ?></h3>
		<br/>
		<div class="row">
			<div class="col-sm-4">
				<label ><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_2'] ?>:</label>
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
					<button type="submit" class="btn btn-info full-row" id="Display_Waveform"><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_1'] ?></button>
				<?php }else{ ?>
					<button type="submit" class="btn btn-info full-row" id="SID_Display_Waveform"><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_1'] ?></button>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
	
<div id="waveforms_container" class="waveforms_container row hidden">
	<span id="waveforms_close_btn" class="glyphicon glyphicon-remove btn-close pointer"></span>
	<div class="col-lg-3 pad-right-40">
		<div class="row">
			<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_3'] ?></h3>
			<div id="signals" class="col-scroll-400"></div>
		</div>
		<div class="row">
			<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_4'] ?></h3>
			<div class="row">
				<div class="col-sm-12">
					<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_5'] ?> :<br/>
					<div class="slider_main" id="slider_main"><div class="slider_button" id="slider_button"></div><input type="hidden" id="simulation_zoom" value="100"/></div><div class="slider_info" id="slider_info">100%</div>
					<br/>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-12">
					<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_6'] ?> :<br/>
					<select class="form-control width-auto" id="waveform_data_type">
						<option value="1"><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_7'] ?></option>
						<option value="2"><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_8'] ?></option>
						<option value="3"><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_9'] ?></option>
					</select>
					<br/>
				</div>
			</div>
			<div class="row">
				<div class="col-sm-4">
				<a href="#" class="btn btn-success" id="btn-download-image" download="waveforms.png"><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_10'] ?></a>
				</div>
				<div class="col-sm-4">
				<a href="#" class="btn btn-info" id="btn-sim-tutorial"><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_11'] ?></a>
				</div>
			</div>
		</div>
	</div>
	<div class="col-lg-9">
		<div class="row">
			<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_12'] ?></h3>
			<div id="canvas_div" class="minh-50">
				<canvas id="WavesCanvas" width="800" height="1" class="pointer">
					<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_13'] ?></canvas>
				<canvas id="BufferCanvas"></canvas>
			</div>
		</div>
		<div class="row hidden_soft" id="waveforms_viewer_tutorial">
			<h3><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_11'] ?></h3>
			<div class="col-sm-12">
				<p>
				<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_14'] ?> :</h4>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_15'] ?><br/>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_16'] ?><br/>
				</p><br/><p>
				<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_17'] ?> :</h4>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_18'] ?><br/>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_19'] ?><br/>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_20'] ?><br/>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_21'] ?><br/>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_22'] ?><br/>
				</p><br/><p>
				<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_23'] ?> :</h4>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_24'] ?><br/>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_25'] ?><br/>
				</p><br/><p>
				<h4><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_26'] ?> :</h4>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_27'] ?><br/>
				<?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_28'] ?><br/>
				</p><br/><p>
				<i><?php echo $messages->text[$_SESSION['vhdl_lang']]['waveforms_viewer_29'] ?></i>
				</p>
			</div>
		</div>
	</div>
</div>
	
	<script src="<?php echo $BASE_URL; ?>/theme/js/waveforms_viewer.js" type="text/javascript" charset="utf-8"></script>