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
            <td><?php echo $entry_rate; ?></td>
            <td><textarea name="citylink_rate" cols="40" rows="5"><?php echo $citylink_rate; ?></textarea></td>
          </tr>
          <tr>
            <td><?php echo $entry_tax_class; ?></td>
            <td><select name="citylink_tax_class_id">
                  <option value="0"><?php echo $text_none; ?></option>
                  <?php foreach ($tax_classes as $tax_class) { ?>
                  <?php if ($tax_class['tax_class_id'] == $citylink_tax_class_id) { ?>
                  <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
          </tr>
           <tr>
                <td><?php echo $entry_zone; ?></td>
                <td><select name="citylink_zone_id" onchange="$('select[name=\'citylink_city_id\']').load('index.php?route=common/localisation/city&token=<?php echo $token; ?>&zone_id=' + this.value);">
                  </select>
                 </td>
           </tr>
         	<tr>
                <td><span class="required">*</span> <?php echo $entry_city; ?></td>
                <td><select name="citylink_city_id" >
                  </select>
                  </td>
            </tr>
          <tr>
            <td><?php echo $entry_description; ?></td>
            <td><textarea name="citylink_description" cols="40" rows="5"><?php echo $citylink_description; ?></textarea></td>
          </tr>
          <tr>
            <td><?php echo $entry_status; ?></td>
            <td><select name="citylink_status">
                <?php if ($citylink_status) { ?>
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
            <td><input type="text" name="citylink_sort_order" value="<?php echo $citylink_sort_order; ?>" size="1" /></td>
          </tr>
        </table>
      </form>
    </div>
  </div>
<script type="text/javascript"><!--
	 $('select[name=\'citylink_zone_id\']').load('index.php?route=common/localisation/zone&token=<?php echo $token; ?>&country_id=<?php echo $country_id; ?>&zone_id=<?php echo $citylink_zone_id; ?>');
     $('select[name=\'citylink_city_id\']').load('index.php?route=common/localisation/city&token=<?php echo $token; ?>&zone_id=<?php echo $citylink_zone_id; ?>&city_id=<?php echo $citylink_city_id; ?>');
//--></script> 
