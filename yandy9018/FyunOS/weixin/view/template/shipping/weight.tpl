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
        <div style="display: inline-block; width: 100%;">
          <div id="tabs" class="vtabs"><a href="#tab-general"><?php echo $tab_general; ?></a>
            <?php foreach ($geo_zones as $geo_zone) { ?>
            <a href="#tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></a>
            <?php } ?>
          </div>
          <div id="tab-general" class="vtabs-content">
            <table class="form">
              <tr>
                <td><?php echo $entry_tax; ?></td>
                <td><select name="weight_tax_class_id">
                    <option value="0"><?php echo $text_none; ?></option>
                    <?php foreach ($tax_classes as $tax_class) { ?>
                    <?php if ($tax_class['tax_class_id'] == $weight_tax_class_id) { ?>
                    <option value="<?php echo $tax_class['tax_class_id']; ?>" selected="selected"><?php echo $tax_class['title']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $tax_class['tax_class_id']; ?>"><?php echo $tax_class['title']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select></td>
              </tr>
              <tr>
                <td><?php echo $entry_status; ?></td>
                <td><select name="weight_status">
                    <?php if ($weight_status) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select></td>
              </tr>
               <tr>
	            <td><?php echo $entry_description; ?></td>
	            <td><textarea name="weight_description" cols="40" rows="5"><?php echo $weight_description; ?></textarea></td>
	          </tr>
              <tr>
                <td><?php echo $entry_sort_order; ?></td>
                <td><input type="text" name="weight_sort_order" value="<?php echo $weight_sort_order; ?>" size="1" /></td>
              </tr>
            </table>
          </div>
          <?php foreach ($geo_zones as $geo_zone) { ?>
          <div id="tab-geo-zone<?php echo $geo_zone['geo_zone_id']; ?>" class="vtabs-content">
            <table class="form">
              <tr>
                <td><?php echo $entry_rate; ?></td>
                <td><input name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_rate" value="<?php echo ${'weight_' . $geo_zone['geo_zone_id'] . '_rate'}; ?>" ></td>
              </tr>
               <tr>
                <td><?php echo $entry_time; ?></td>
                <td><input name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_time" value="<?php echo ${'weight_' . $geo_zone['geo_zone_id'] . '_time'}; ?>" ></td>
              </tr>
              <tr>
                <td><?php echo $entry_status; ?></td>
                <td><select name="weight_<?php echo $geo_zone['geo_zone_id']; ?>_status">
                    <?php if (${'weight_' . $geo_zone['geo_zone_id'] . '_status'}) { ?>
                    <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                    <option value="0"><?php echo $text_disabled; ?></option>
                    <?php } else { ?>
                    <option value="1"><?php echo $text_enabled; ?></option>
                    <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                    <?php } ?>
                  </select></td>
              </tr>
            </table>
          </div>
          <?php } ?>
        </div>
      </form>
    </div>
  </div>
<script type="text/javascript"><!--
$('#tabs a').tabs(); 
//--></script> 
