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
            <td><span class="required">*</span> <?php echo $entry_auth_id; ?></td>
            <td><input type="text" name="perpetual_payments_auth_id" value="<?php echo $perpetual_payments_auth_id; ?>" />
              <?php if ($error_auth_id) { ?>
              <span class="error"><?php echo $error_auth_id; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_auth_pass; ?></td>
            <td><input type="text" name="perpetual_payments_auth_pass" value="<?php echo $perpetual_payments_auth_pass; ?>" />
              <?php if ($error_auth_pass) { ?>
              <span class="error"><?php echo $error_auth_pass; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_test; ?></td>
            <td><?php if ($perpetual_payments_test) { ?>
              <input type="radio" name="perpetual_payments_test" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="perpetual_payments_test" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="perpetual_payments_test" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="perpetual_payments_test" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="perpetual_payments_total" value="<?php echo $perpetual_payments_total; ?>" /></td>
          </tr>          
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="perpetual_payments_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $perpetual_payments_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="perpetual_payments_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $perpetual_payments_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="perpetual_payments_status">
                <?php if ($perpetual_payments_status) { ?>
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
            <td><input type="text" name="perpetual_payments_sort_order" value="<?php echo $perpetual_payments_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
