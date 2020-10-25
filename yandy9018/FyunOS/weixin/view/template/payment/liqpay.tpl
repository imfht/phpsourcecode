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
            <td><span class="required">*</span> <?php echo $entry_merchant; ?></td>
            <td><input type="text" name="liqpay_merchant" value="<?php echo $liqpay_merchant; ?>" />
              <?php if ($error_merchant) { ?>
              <span class="error"><?php echo $error_merchant; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_signature; ?></td>
            <td><input type="text" name="liqpay_signature" value="<?php echo $liqpay_signature; ?>" />
              <?php if ($error_signature) { ?>
              <span class="error"><?php echo $error_signature; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_type; ?></td>
            <td><select name="liqpay_type">
                <?php if ($liqpay_type == 'liqpay') { ?>
                <option value="liqpay" selected="selected"><?php echo $text_pay; ?></option>
                <?php } else { ?>
                <option value="liqpay"><?php echo $text_pay; ?></option>
                <?php } ?>
                <?php if ($liqpay_type == 'card') { ?>
                <option value="card" selected="selected"><?php echo $text_card; ?></option>
                <?php } else { ?>
                <option value="card"><?php echo $text_card; ?></option>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_total; ?></td>
            <td><input type="text" name="liqpay_total" value="<?php echo $liqpay_total; ?>" /></td>
          </tr>          
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="liqpay_order_status_id">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $liqpay_order_status_id) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_geo_zone; ?></td>
            <td><select name="liqpay_geo_zone_id">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $liqpay_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="liqpay_status">
                <?php if ($liqpay_status) { ?>
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
            <td><input type="text" name="liqpay_sort_order" value="<?php echo $liqpay_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
