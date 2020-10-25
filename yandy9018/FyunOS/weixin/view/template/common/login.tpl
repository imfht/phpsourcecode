
<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $lang; ?>" xml:lang="<?php echo $lang; ?>">
<head>
<title>登录</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
<meta content="telephone=no" name="format-detection" />
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



<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-1.8.9.custom.min.js"></script>

<script type="text/javascript" src="view/javascript/jquery/ui/external/jquery.bgiframe-2.1.2.js"></script>
<script type="text/javascript" src="view/javascript/jquery/tabs.js"></script>
<script type="text/javascript" src="view/javascript/jquery/superfish/js/superfish.js"></script>
<link rel="stylesheet" type="text/css" href="view/javascript/upload/fileuploader.css" />
<script type="text/javascript" src="view/javascript/upload/fileuploader.js"></script>


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
<div>
<div class="content1">
         <div style=" padding-top:100px;">
    <div class="content" style="background-color:#FFF" id="login">
      <h1>订单管理</h1>
      <?php if ($success) { ?>
      <div class="success"><?php echo $success; ?></div>
      <?php } ?>
      <?php if ($error_warning) { ?>
      <div class="warning"><?php echo $error_warning; ?></div>
      <?php } ?>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table style="width: 100%;">
           <tr>
            <td><?php echo $entry_username; ?><br />
              <input type="text" name="username" value="" style="margin-top: 4px;width:330px;" />
              <br />
              <br />
              <?php echo $entry_password; ?><br />
              <input type="password" name="password" value="" style="margin-top: 4px;width:330px"/>
              <!--
               <br />
               <a href="<?php echo $forgotten; ?>"><?php echo $text_forgotten; ?></a>
              -->
              </td>
          </tr>
          <tr>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td><input type="button" onClick="$('#form').submit();" class="btn btn-primary"  value="<?php echo $button_login; ?>"></td>
          </tr>
        </table>
       
      </form>
     </div>
  </div>
<script type="text/javascript"><!--
$('#form input').keydown(function(e) {
    if (e.keyCode == 13) {
        $('#form').submit();
    }
});
//--></script> 

    </div>
    
</div>

   <!-- Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog"  style="width:300px;">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">关于方云订单管理系统</h4>
            </div>
            <div class="modal-body">
            <p>系统版本：V1.0</p>
            <p id="dateto"></p>
            <p>版权所有：方云工作室</p>
            <p>官方网站：<a href="http://fyun.mobi" target="_blank">http://fyun.mobi</a></p>
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