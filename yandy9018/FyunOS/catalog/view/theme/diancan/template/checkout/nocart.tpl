<?php echo $header; ?>
<body>
    
<div data-role="page" style="background-color:#F0F0F0;">
  
<div data-role="header" data-position="fixed">
</div>

<div data-role="content"  style="background-color:#F0F0F0; padding-bottom:10px;" data-theme="a">

<div id="ui-body-test" class="ui-body ui-body-a" style="margin-bottom:1em;">
	
        <p><div style="TEXT-ALIGN: center; padding-top:10px;"><img src="catalog/view/theme/diancan/image/icon_cart_empty.png" width="81px;"></p><br>

			<h1><?php echo $text_empty; ?></h1>
			
	<a href="index.php?route=product/category&path=0" data-ajax="false" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-mini"><?php echo $entry_category; ?></a>

		</div>
</div>
</div>

          
            
 <div data-role="footer" data-position="fixed">
<?php echo $nav; ?>
</div>
		</div>

	</body>
</html>