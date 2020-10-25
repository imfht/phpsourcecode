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
		<td><span class="required">*</span> <?php echo $entry_trade_type; ?><br>(请根据对应的接口填入正确的参数)</td>
          <td>
			 <?php if ($alipay_trade_type=='trade_create_by_buyer') { ?>
				双接口:&nbsp;<input type="radio" name="alipay_trade_type" value="trade_create_by_buyer"  checked="true"/>&nbsp;&nbsp;
				直接到帐:&nbsp;<input type="radio" name="alipay_trade_type" value="create_direct_pay_by_user"/>&nbsp;&nbsp;
				担保接口:&nbsp;<input type="radio" name="alipay_trade_type" value="create_partner_trade_by_buyer"/>&nbsp;&nbsp;
			<?php } else if($alipay_trade_type=='create_direct_pay_by_user') {  ?>
				双接口:&nbsp;<input type="radio" name="alipay_trade_type" value="trade_create_by_buyer"/>&nbsp;&nbsp;
				直接到帐:&nbsp;<input type="radio" name="alipay_trade_type" value="create_direct_pay_by_user"  checked="true"/>&nbsp;&nbsp;
				担保接口:&nbsp;<input type="radio" name="alipay_trade_type" value="create_partner_trade_by_buyer"/>&nbsp;&nbsp;
			<?php } else {  ?>
				双接口:&nbsp;<input type="radio" name="alipay_trade_type" value="trade_create_by_buyer"/>&nbsp;&nbsp;
				直接到帐:&nbsp;<input type="radio" name="alipay_trade_type" value="create_direct_pay_by_user"/>&nbsp;&nbsp;
				担保接口:&nbsp;<input type="radio" name="alipay_trade_type" value="create_partner_trade_by_buyer"  checked="true"/>&nbsp;&nbsp;
			<?php } ?>
		</td>
        </tr>
	  	<tr>
          <td><span class="required">*</span> <?php echo $entry_partner; ?></td>
          <td><input type="text" name="alipay_partner" value="<?php echo $alipay_partner; ?>" size="50" />
	  <?php if ($error_partner) { ?>
            <span class="error"><?php echo $error_partner; ?></span>
            <?php } ?></td>
        </tr>

		<tr>
          <td><span class="required">*</span> <?php echo $entry_security_code; ?></td>
          <td><input type="text" name="alipay_security_code" value="<?php echo $alipay_security_code; ?>" size="50" />
	  <?php if ($error_secrity_code) { ?>
            <span class="error"><?php echo $error_secrity_code; ?></span>
            <?php } ?></td>
        </tr>
	   <tr>
          <td><span class="required">*</span> <?php echo $entry_seller_email; ?></td>
          <td><input type="text" name="alipay_seller_email" value="<?php echo $alipay_seller_email; ?>" size="50" />
            <?php if ($error_email) { ?>
            <span class="error"><?php echo $error_email; ?></span>
            <?php } ?></td>
        </tr>
	
        <tr>
          <td><?php echo $entry_order_status; ?></td>
          <td><select name="alipay_order_status_id">
              <?php foreach ($order_statuses as $order_status) { ?>
              <?php if ($order_status['order_status_id'] == $alipay_order_status_id) { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select></td>
        </tr>
        <tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="alipay_status">
              <?php if ($alipay_status) { ?>
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
          <td><input type="text" name="alipay_sort_order" value="<?php echo $alipay_sort_order; ?>" size="1" /></td>
        </tr>
		 <tr>
          <td>&nbsp;</td>
          <td>使用注意已经存在CNY的人民币汇率设置。Code为CNY</td>
        </tr>
      </table>
    </form>
  </div>
  </div>
