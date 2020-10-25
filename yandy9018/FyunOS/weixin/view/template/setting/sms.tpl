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
      <div id="tabs" class="htabs"><a href="#tab-mail"><?php echo $tab_sms; ?></a></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        
        <div id="tab-mail">
          <table class="form">
           
            <tr>
              <td><?php echo $entry_sms_url; ?></td>
              <td><input type="text" name="config_sms_url" value="<?php echo $config_sms_url; ?>" /></td>
            </tr>
            
             <tr>
              <td><?php echo $entry_sms_ac; ?></td>
              <td><input type="text" name="config_sms_ac" value="<?php echo $config_sms_ac; ?>" /></td>
            </tr>
            
             <tr>
              <td><?php echo $entry_sms_authkey; ?></td>
              <td><input type="text" name="config_sms_authkey" value="<?php echo $config_sms_authkey; ?>" /></td>
            </tr>
            
            
             <tr>
              <td><?php echo $entry_sms_cgid; ?></td>
              <td><input type="text" name="config_sms_cgid" value="<?php echo $config_sms_cgid; ?>" />
             </td>
            </tr>
            
            
             <tr>
              <td><?php echo $entry_sms_csid; ?></td>
              <td><input type="text" name="config_sms_csid" value="<?php echo $config_sms_csid; ?>" />
</td>
            </tr>

          </table>
        </div>
        
      </form>
    </div>
  </div>


<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 
