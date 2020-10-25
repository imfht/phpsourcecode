<?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2><?php echo $heading_title; ?></h2>
       <div class="buttons"><a onclick="$('#form').submit();" class="btn btn-primary"><span><?php echo $button_save; ?></span></a> <a onclick="location = '<?php echo $cancel; ?>';" class="btn"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <div id="tabs" class="htabs"><a href="#tab-mail"><?php echo $tab_wechat; ?></a>  <a href="#tab-menu"><i class="icon-exclamation-sign"></i>自定义菜单</a></div>
     <?php if ($config_wechat_status==0) { ?> 
      <div class="alert alert-danger"><b>未通过微信token验证！</b><br />
<br />
URL地址：<?php echo $config_url; ?>/index.php?route=common/weixin<br />
TOKEN：<?php echo $config_wechat_token; ?></div>
       <?php } ?> 
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        
        <div id="tab-mail">
          <table class="form">
           
            <tr>
              <td><?php echo $entry_wechat_appid; ?></td>
              <td><input type="text" name="config_wechat_appid" value="<?php echo $config_wechat_appid; ?>" /></td>
            </tr>
            
             <tr>
              <td><?php echo $entry_wechat_appsecret; ?></td>
              <td><input type="text" name="config_wechat_appsecret" value="<?php echo $config_wechat_appsecret; ?>" /></td>
            </tr>
            
             <tr>
              <td><?php echo $entry_wechat_token; ?></td>
              <td><input type="text" name="config_wechat_token" value="<?php echo $config_wechat_token; ?>" /></td>
            </tr>
            
            
             <tr>
              <td><?php echo $entry_wechat_reply; ?></td>
              <td><textarea name="config_wechat_reply" cols="40" rows="5"><?php echo $config_wechat_reply; ?></textarea>
             </td>
            </tr>
            
            
             <tr>
              <td><?php echo $entry_wechat_attention; ?></td>
              <td><textarea name="config_wechat_attention" cols="40" rows="5"><?php echo $config_wechat_attention; ?></textarea>
</td>
            </tr>

          </table>
        </div>
        <div id="tab-menu" style="height:auto;">
       
       <iframe src="<?php echo $config_url; ?>/api/menu/menu.php?id=<?php echo $config_wechat_appid; ?>&key=<?php echo $config_wechat_appsecret; ?>" width="100%" height="1000px;" frameborder="0" scrolling="no" noresize /></iframe>
        </div>
      </form>
    </div>
  </div>


<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 
