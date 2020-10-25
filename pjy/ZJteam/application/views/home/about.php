<?php include 'application/views/home/header.php'?>	
<script>
$(function(){
	$("#about").addClass("active");
});
</script>
<div class="main_btm"><!-- start main_btm -->
	<div class="container">
		<div class="main row">
			<?php echo $about->content;?>
		</div>
	</div>
</div>

<?php include 'application/views/home/footer.php'?>	