 <div class="box" >
    <div class="content" id="login">
      <h1>登录Fyun管理</h1>
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
              <input type="text" name="username" value="<?php echo $username; ?>" style="margin-top: 4px;width:330px;" />
              <br />
              <br />
              <?php echo $entry_password; ?><br />
              <input type="password" name="password" value="<?php echo $password; ?>" style="margin-top: 4px;width:330px"/>
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
            <td><input type="button" onclick="$('#form').submit();" class="btn btn-primary"  value="<?php echo $button_login; ?>"></td>
          </tr>
        </table>
        <?php if ($redirect) { ?>
        <input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
        <?php } ?>
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
