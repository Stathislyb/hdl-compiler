<?php 
if( !isset($db) ){
	header("location: //".$_SERVER["SERVER_NAME"]); 
	exit();
}
?>
<?php 
	if( empty($_GET['short_code']) ){
		if(isset($_GET['page'])){
			$page=20*($_GET['page']-1);
		}else{
			$page=20*0;
		}
		if(isset($_POST['name'])){
			$name=$_POST['name'];
		}else{
			$name='';
		} ?>

		<div class="form-group">
			<form action="" method="post" id="filter_libraries" onsubmit="return false;">
				<input name="name" type="text" value="<?php echo $name; ?>" class="form-control" placeholder="Search Components" id="filter_libs_input"/>
			</form>
		</div>
		<div id="libraries_container">
			<?php require('pages/extra/list-all-libraries.php'); ?>
		</div>
		<?php		
	}else{
		require('pages/extra/display-library.php');
	}
	
?>