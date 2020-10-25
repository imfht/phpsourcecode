<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<?php if ($description) { ?>
<meta name="description" content="<?php echo $description; ?>" />
<?php } ?>
<?php if ($keywords) { ?>
<meta name="keywords" content="<?php echo $keywords; ?>" />
<?php } ?>
<?php foreach ($links as $link) { ?>
<link href="<?php echo $link['href']; ?>" rel="<?php echo $link['rel']; ?>" />
<?php } ?>
  <!-- bootstrap -->
    <link href="view/stylesheet/bootstrap/bootstrap.css" rel="stylesheet" />
 <link href="view/stylesheet/bootstrap/bootstrap-overrides.css" type="text/css" rel="stylesheet" />
<script src="view/bootstrap/js/jquery.js"></script>
<script src="view/bootstrap/js/bootstrap.js"></script>



<link rel="stylesheet" type="text/css" href="view/stylesheet/stylesheet.css" />
  

    <!-- libraries -->
    <link href="view/stylesheet/lib/jquery-ui-1.10.2.custom.css" rel="stylesheet" type="text/css" />
    <link href="view/stylesheet/lib/font-awesome.css" type="text/css" rel="stylesheet" />

    <!-- global styles -->
    <link rel="stylesheet" type="text/css" href="view/stylesheet/compiled/layout.css">
    <link rel="stylesheet" type="text/css" href="view/stylesheet/compiled/elements.css">
    <link rel="stylesheet" type="text/css" href="view/stylesheet/compiled/icons.css">

    <!-- this page specific styles -->
    <link rel="stylesheet" href="view/stylesheet/compiled/index.css" type="text/css" media="screen" />

    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
<?php foreach ($styles as $style) { ?>
<link rel="<?php echo $style['rel']; ?>" type="text/css" href="<?php echo $style['href']; ?>" media="<?php echo $style['media']; ?>" />
<?php } ?>

<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.9.custom.min.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/jquery/ui/themes/flick/jquery-ui-1.8.16.custom.css" />
<script type="text/javascript" src="view/javascript/jquery/ui/external/jquery.bgiframe-2.1.2.js"></script>
<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="view/javascript/jquery/superfish/js/superfish.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/upload/fileuploader.css" />
<script type="text/javascript" src="view/javascript/upload/fileuploader.js"></script>



<?php foreach ($scripts as $script) { ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php } ?>
<script type="text/javascript">
//-----------------------------------------
// Confirm Actions (delete, uninstall)
//-----------------------------------------

$(document).ready(function(){
	// Confirm Delete
    $('#form').submit(function(){
        if ($(this).attr('action').indexOf('delete',1) != -1) {
            if (!confirm ('<?php echo $text_confirm; ?>')) {
                return false;
            }
        }
    });

    // Confirm Uninstall
    $('a').click(function(){
        if ($(this).attr('href') != null && $(this).attr('href').indexOf('uninstall',1) != -1) {
            if (!confirm ('<?php echo $text_confirm; ?>')) {
                return false;
            }
        }
    });
});
</script>
</head>
<body>
<div id="container">
		<div id="header">
	  <?php echo $header; ?>
	</div>
<div id="content">
	  	<?php echo $content; ?>
	</div>
	<div>
		<?php echo $footer; ?>
	</div>
</div>
		<div id="myguide" class="modal hide fade" style="display: none; ">
        
        <?php if ($store_status==1) { ?>
            <div class="modal-header">
              <a class="close" data-dismiss="modal">×</a>
              <h3>店铺当前运营状态 : 运营中</h3>
            </div>
            <div class="modal-body">
            	关闭店铺之后您将无法获取到客户的订单信息。
            </div>
            <div class="modal-footer">
              <a id="close" href="#" class="btn " data-dismiss="modal">关闭店铺</a>
            </div>
          <?php }else { ?>
            <div class="modal-header">
              <a class="close" data-dismiss="modal">×</a>
              <h3>店铺当前运营状态 : 运营中</h3>
            </div>
            <div class="modal-body">
            	新的一天，新的开始，祝您在新的一天顺顺利利。
            </div>
            <div class="modal-footer">
              <a id="open" href="#" class="btn " data-dismiss="modal">开启店铺</a>
            </div>
            <?php } ?>
            

         
<script>
$('#close').bind('click',function(){
  $.ajax({
		  url: 'index.php?route=setting/setting/updateStatus&status=0&token=<?php echo $token; ?>',
		  type: 'get',
		  success: function() {
			  	alert('店铺关闭成功')
			  }
	  });	
});
$('#open').bind('click',function(){
  $.ajax({
		  url: 'index.php?route=setting/setting/updateStatus&status=1&token=<?php echo $token; ?>',
		  type: 'get',
		  success: function() {
			  	alert('店铺开启成功')
			  }
	  });	
});
</script>
<!-- scripts -->
<!--    <script src="view/javascript/jquery/bootstrap.min.js"></script>
    <script src="view/javascript/jquery/bootstrap.datepicker.js"></script>-->

    <script src="view/javascript/jquery/theme.js"></script>

</div>

</body>

</html>