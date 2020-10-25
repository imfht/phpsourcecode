<div  class="topBtn_mod _hidden"><a  id="topBtn" style=" display:none;"  href="javascript:void(0)"  title="回到顶部"  alt="回到顶部">向上</a></div>
<div> </div>
</body>
</html>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/js/jquery1.71.js"></script>
<script type="text/javascript">
	
	$(window).bind('scroll', function(){
	
		if ($(this).scrollTop() > 0) { $('#topBtn').show(); } else { $('#topBtn').hide();}			
 
	});
	
	$('#topBtn').click(function(e){
		e.stopPropagation();
		$('body,html').animate({scrollTop: 0}, 300);
		return false;
	});

</script>
<?php echo get_option('mytheme_analytics'); ?>
