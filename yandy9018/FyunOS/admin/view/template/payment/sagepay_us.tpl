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
            <td><span class="required">*</span> <?php echo $entry_merchant_id; ?></td>
            <td><input type="text" name="sagepay_us_merchant_id" value="<?php echo $sagepay_us_merchant_id; ?>" />
              <?php if ($error_merchant_id) { ?>
              <span class="error"><?php echo $error_merchant_id; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_merchant_key; ?></td>
            <td><input type="text" name="sagepay_us_merchant_key" value="<?php echo $sagepay_us_merchant_key; ?>" />
              <?php if ($error_merchant_key) { ?>
              <span class="error"><?php echo $error_merchant_key; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="sagepay_us_total" value="<?php echo $sagepay_us_total; ?>" /></td>
          </tr>          
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="sagepay_us_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $sagepay_us_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="sagepay_us_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $sagepay_us_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="sagepay_us_status">
                <?php if ($sagepay_us_status) { ?>
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
            <td><input type="text" name="sagepay_us_sort_order" value="<?php echo $sagepay_us_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
