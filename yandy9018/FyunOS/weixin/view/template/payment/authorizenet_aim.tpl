  <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2><?php echo $heading_title; ?></h2>
      <div class="buttons"><button onclick="$('#form').submit();" class="btn btn-primary"><?php echo $button_save; ?></button> <button onclick="location = '<?php echo $cancel; ?>';" class="btn"><?php echo $button_cancel; ?></button></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_login; ?></td>
            <td><input type="text" name="authorizenet_aim_login" value="<?php echo $authorizenet_aim_login; ?>" />
              <?php if ($error_login) { ?>
              <span class="error"><?php echo $error_login; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_key; ?></td>
            <td><input type="text" name="authorizenet_aim_key" value="<?php echo $authorizenet_aim_key; ?>" />
              <?php if ($error_key) { ?>
              <span class="error"><?php echo $error_key; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_hash; ?></td>
            <td><input type="text" name="authorizenet_aim_hash" value="<?php echo $authorizenet_aim_hash; ?>" />
              <?php if ($error_hash) { ?>
              <span class="error"><?php echo $error_hash; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_server; ?></td>
            <td><select name="authorizenet_aim_server">
                <?php if ($authorizenet_aim_server == 'live') { ?>
                <option value="live" selected="selected"><?php echo $text_live; ?></option>
                <?php } else { ?>
                <option value="live"><?php echo $text_live; ?></option>
                <?php } ?>
                <?php if ($authorizenet_aim_server == 'test') { ?>
                <option value="test" selected="selected"><?php echo $text_test; ?></option>
                <?php } else { ?>
                <option value="test"><?php echo $text_test; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_mode; ?></td>
            <td><select name="authorizenet_aim_mode">
                <?php if ($authorizenet_aim_mode == 'live') { ?>
                <option value="live" selected="selected"><?php echo $text_live; ?></option>
                <?php } else { ?>
                <option value="live"><?php echo $text_live; ?></option>
                <?php } ?>
                <?php if ($authorizenet_aim_mode == 'test') { ?>
                <option value="test" selected="selected"><?php echo $text_test; ?></option>
                <?php } else { ?>
                <option value="test"><?php echo $text_test; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_method; ?></td>
            <td><select name="authorizenet_aim_method">
                <?php if ($authorizenet_aim_method == 'authorization') { ?>
                <option value="authorization" selected="selected"><?php echo $text_authorization; ?></option>
                <?php } else { ?>
                <option value="authorization"><?php echo $text_authorization; ?></option>
                <?php } ?>
                <?php if ($authorizenet_aim_method == 'capture') { ?>
                <option value="capture" selected="selected"><?php echo $text_capture; ?></option>
                <?php } else { ?>
                <option value="capture"><?php echo $text_capture; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="authorizenet_aim_total" value="<?php echo $authorizenet_aim_total; ?>" /></td>
          </tr>
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="authorizenet_aim_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $authorizenet_aim_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="authorizenet_aim_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $authorizenet_aim_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="authorizenet_aim_status">
                <?php if ($authorizenet_aim_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="authorizenet_aim_sort_order" value="<?php echo $authorizenet_aim_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>