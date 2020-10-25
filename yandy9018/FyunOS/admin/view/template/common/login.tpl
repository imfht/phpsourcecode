<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<title>登录系统</title>

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
</head>
<body>
<div>
<div>
         <div style=" padding-top:100px;">
    <div class="content" style="background-color:#FFF" id="login">
      <h1>微餐厅管理系统</h1>
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

    <script src="view/javascript/jquery/theme.js"></script>

</body>

</html>