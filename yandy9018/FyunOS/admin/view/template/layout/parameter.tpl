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
<div id="content" class="content1">
      <?php echo $content; ?>
  </div>
  <div>
    <?php echo $footer; ?>
  </div>
</div>

   <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog"  style="width:300px;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">关于方云OS</h4>
            </div>
            <div class="modal-body">
            <p>系统版本：V1.0</p>
           
            <p>版权所有：方云工作室</p>
            <p>官方网站：<a href="http://fyunos.duapp.com" target="_blank"> http://fyunos.duapp.com </a></p>
            <p><img src="view/image/wx.png"></p>
             
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-dismiss="modal">关闭</button>
            </div>
          </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->


     
         
<script>
$('.shop_close').bind('click',function(){
  $.ajax({
      url: 'index.php?route=setting/setting/updateStatus&status=0&token=<?php echo $token; ?>&m=<?php echo SNAME; ?>',
      type: 'get',
      success: function() {
        $('#shopstatus').html('<i class="icon-play"></i>');
        $(".shop_open").css('display','block'); 
        $(".shop_close").css('display','none'); 
        }
    }); 
});
$('.shop_open').bind('click',function(){
  $.ajax({
      url: 'index.php?route=setting/setting/updateStatus&status=1&token=<?php echo $token; ?>&m=<?php echo SNAME; ?>',
      type: 'get',
      success: function() {
        $('#shopstatus').html('<i class="icon-pause"></i>');
         $(".shop_open").css('display','none'); 
          $(".shop_close").css('display','block'); 
        }
    }); 
});
</script>
<!-- scripts -->
<!--    <script src="view/javascript/jquery/bootstrap.min.js"></script>
    <script src="view/javascript/jquery/bootstrap.datepicker.js"></script>-->

    <script src="view/javascript/jquery/theme.js"></script>

</body>

</html>