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
            <td><input type="text" name="google_checkout_merchant_id" value="<?php echo $google_checkout_merchant_id; ?>" />
              <?php if ($error_merchant_id) { ?>
              <span class="error"><?php echo $error_merchant_id; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_merchant_key; ?></td>
            <td><input type="text" name="google_checkout_merchant_key" value="<?php echo $google_checkout_merchant_key; ?>" />
              <?php if ($error_merchant_key) { ?>
              <span class="error"><?php echo $error_merchant_key; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_test; ?></td>
            <td><?php if ($google_checkout_test) { ?>
              <input type="radio" name="google_checkout_test" value="1" checked="checked" />
              <?php echo $text_yes; ?>
              <input type="radio" name="google_checkout_test" value="0" />
              <?php echo $text_no; ?>
              <?php } else { ?>
              <input type="radio" name="google_checkout_test" value="1" />
              <?php echo $text_yes; ?>
              <input type="radio" name="google_checkout_test" value="0" checked="checked" />
              <?php echo $text_no; ?>
              <?php } ?></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="google_checkout_status">
                <?php if ($google_checkout_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
