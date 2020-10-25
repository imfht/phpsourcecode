  <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">   
   <div class="heading">
      <h2> <?php echo $heading_title; ?></h2>
      <div class="buttons"><button onclick="$('#form').submit();" class="btn btn-primary"  ><?php echo $button_save; ?></button> <button onclick="location = '<?php echo $cancel; ?>';" class="btn"><?php echo $button_cancel; ?></button></div>
    </div>
    <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <table class="form">
          <tr>
            <td><span class="required">*</span> <?php echo $entry_name; ?></td>
            <td><input type="text" name="name" value="<?php echo $name; ?>" />
              <?php if ($error_name) { ?>
              <span class="error"><?php echo $error_name; ?></span>
              <?php } ?></td>
          </tr>
          <tr>
            <td><span class="required">*</span> <?php echo $entry_description; ?></td>
            <td><input type="text" name="description" value="<?php echo $description; ?>" />
              <?php if ($error_description) { ?>
              <span class="error"><?php echo $error_description; ?></span>
              <?php } ?></td>
          </tr>
        </table>
        <br />
        <table id="zone-to-geo-zone" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $entry_country; ?></td>
              <td class="left"><?php echo $entry_zone; ?></td>
              <td></td>
            </tr>
          </thead>
          <?php $zone_to_geo_zone_row = 0; ?>
          <?php foreach ($zone_to_geo_zones as $zone_to_geo_zone) { ?>
          <tbody id="zone-to-geo-zone-row<?php echo $zone_to_geo_zone_row; ?>">
            <tr>
              <td class="left"><select name="zone_to_geo_zone[<?php echo $zone_to_geo_zone_row; ?>][zone_id]" id="country<?php echo $zone_to_geo_zone_row; ?>" onchange="$('#zone<?php echo $zone_to_geo_zone_row; ?>').load('index.php?route=localisation/geo_zone/city&token=<?php echo $token; ?>&zone_id=' + this.value);">
                  <?php foreach ($countries as $country) { ?>
                  <?php  if ($country['zone_id'] == $zone_to_geo_zone['zone_id']) { ?>
                  <option value="<?php echo $country['zone_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $country['zone_id']; ?>"><?php echo $country['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
              <td class="left"><select name="zone_to_geo_zone[<?php echo $zone_to_geo_zone_row; ?>][city_id]" id="zone<?php echo $zone_to_geo_zone_row; ?>">
                </select></td>
              <td class="left"><a onclick="$('#zone-to-geo-zone-row<?php echo $zone_to_geo_zone_row; ?>').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>
            </tr>
          </tbody>
          <?php $zone_to_geo_zone_row++; ?>
          <?php } ?>
          <tfoot>
            <tr>
              <td colspan="2"></td>
              <td class="left"><a onclick="addGeoZone();" class="button"><span><?php echo $button_add_geo_zone; ?></span></a></td>
            </tr>
          </tfoot>
        </table>
      </form>
    </div>
  </div>

<script type="text/javascript"><!--
$('#zone-id').load('index.php?route=localisation/geo_zone/city&token=<?php echo $token; ?>&zone_id=' + $('#zone-id').attr('value') + '&city_id=0');
//--></script>
<?php $zone_to_geo_zone_row = 0; ?>
<?php foreach ($zone_to_geo_zones as $zone_to_geo_zone) { ?>
<script type="text/javascript"><!--
$('#zone<?php echo $zone_to_geo_zone_row; ?>').load('index.php?route=localisation/geo_zone/city&token=<?php echo $token; ?>&zone_id=<?php echo $zone_to_geo_zone['zone_id']; ?>&city_id=<?php echo $zone_to_geo_zone['city_id']; ?>');
//--></script>
<?php $zone_to_geo_zone_row++; ?>
<?php } ?>
<script type="text/javascript"><!--
var zone_to_geo_zone_row = <?php echo $zone_to_geo_zone_row; ?>;

function addGeoZone() {
	html  = '<tbody id="zone-to-geo-zone-row' + zone_to_geo_zone_row + '">';
	html += '<tr>';
	html += '<td class="left"><select name="zone_to_geo_zone[' + zone_to_geo_zone_row + '][zone_id]" id="country' + zone_to_geo_zone_row + '" onchange="$(\'#zone' + zone_to_geo_zone_row + '\').load(\'index.php?route=localisation/geo_zone/city&token=<?php echo $token; ?>&zone_id=\' + this.value + \'&city_id=0\');">';
	<?php foreach ($countries as $country) { ?>
	html += '<option value="<?php echo $country['zone_id']; ?>"><?php echo addslashes($country['name']); ?></option>';
	<?php } ?>   
	html += '</select></td>';
	html += '<td class="left"><select name="zone_to_geo_zone[' + zone_to_geo_zone_row + '][city_id]" id="zone' + zone_to_geo_zone_row + '"></select></td>';
	html += '<td class="left"><a onclick="$(\'#zone-to-geo-zone-row' + zone_to_geo_zone_row + '\').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>';
	html += '</tr>';
	html += '</tbody>';
	
	$('#zone-to-geo-zone > tfoot').before(html);
		
	$('#zone' + zone_to_geo_zone_row).load('index.php?route=localisation/geo_zone/city&token=<?php echo $token; ?>&zone_id=' + $('#country' + zone_to_geo_zone_row).attr('value') + '&city_id=0');
	
	zone_to_geo_zone_row++;
}
//--></script> 
