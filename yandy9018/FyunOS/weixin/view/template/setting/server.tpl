<?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2> <?php echo $heading_title; ?></h2>
      <div class="buttons"><a onclick="$('#form').submit();" class="btn btn-primary"><span><?php echo $button_save; ?></span></a> <a onclick="location = '<?php echo $cancel; ?>';" class="btn"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <div id="tab-server">
       <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
          <table class="form">
            <tr>
              <td><?php echo $entry_use_ssl; ?></td>
              <td><?php if ($config_use_ssl) { ?>
                <input type="radio" name="config_use_ssl" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_use_ssl" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_use_ssl" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_use_ssl" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_seo_url; ?></td>
              <td><?php if ($config_seo_url) { ?>
                <input type="radio" name="config_seo_url" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_seo_url" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_seo_url" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_seo_url" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_maintenance; ?></td>
              <td><?php if ($config_maintenance) { ?>
                <input type="radio" name="config_maintenance" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_maintenance" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_maintenance" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_maintenance" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_encryption; ?></td>
              <td><input type="text" name="config_encryption" value="<?php echo $config_encryption; ?>" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_compression; ?></td>
              <td><input type="text" name="config_compression" value="<?php echo $config_compression; ?>" size="3" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_debug_log; ?></td>
              <td><?php if ($config_debug) { ?>
                <input type="radio" name="config_debug" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_debug" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_debug" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_debug" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_error_display; ?></td>
              <td><?php if ($config_error_display) { ?>
                <input type="radio" name="config_error_display" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_error_display" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_error_display" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_error_display" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_error_log; ?></td>
              <td><?php if ($config_error_log) { ?>
                <input type="radio" name="config_error_log" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_error_log" value="0" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="config_error_log" value="1" />
                <?php echo $text_yes; ?>
                <input type="radio" name="config_error_log" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_error_filename; ?></td>
              <td><input type="text" name="config_error_filename" value="<?php echo $config_error_filename; ?>" />
                <?php if ($error_error_filename) { ?>
                <span class="help-inline error"><?php echo $error_error_filename; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_google_analytics; ?></td>
              <td><textarea name="config_google_analytics" cols="40" rows="5"><?php echo $config_google_analytics; ?></textarea></td>
            </tr>
          </table>
        </div>
      </form>
    </div>
  </div>


<script type="text/javascript"><!--
$('#tabs a').tabs();
//--></script> 
