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
          <td><span class="required">*</span> <?php echo $entry_country; ?></td>
          <td><select name="country_id" id="country_id" onchange="$('select[name=\'zone_id\']').load('index.php?route=localisation/city/zone&token=<?php echo $token; ?>&country_id=' + this.value);">
              <option value=""><?php echo $text_select; ?></option>
	      <?php foreach ($countries as $country) { ?>
              <?php if ($country['country_id'] == $country_id) { ?>
              <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
              <?php } else { ?>
              <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
              <?php } ?>
              <?php } ?>
            </select>
	    <?php if ($error_country) { ?>
            <span class="error"><?php echo $error_country; ?></span>
            <?php } ?></td>
       </tr>
		<tr>
          <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
          <td><select name="zone_id" id="zone_id">
            </select>
	    <?php if ($error_zone) { ?>
            <span class="error"><?php echo $error_zone; ?></span>
            <?php } ?></td>
        </tr>
        <tr>
          <td><span class="required">*</span> <?php echo $entry_name; ?></td>
          <td><input type="text" name="name" value="<?php echo $name; ?>" />
            <?php if ($error_name) { ?>
            <span class="error"><?php echo $error_name; ?></span>
            <?php } ?></td>
        </tr>
       
		<tr>
          <td><?php echo $entry_status; ?></td>
          <td><select name="status">
              <?php if ($status) { ?>
              <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
              <option value="0"><?php echo $text_disabled; ?></option>
              <?php } else { ?>
              <option value="1"><?php echo $text_enabled; ?></option>
              <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
              <?php } ?>
            </select></td>
        </tr>
      </table>
      <script type="text/javascript"><!--
		  $('select[name=\'zone_id\']').load('index.php?route=localisation/city/zone&token=<?php echo $token; ?>&country_id=<?php echo $country_id; ?>&zone_id=<?php echo $zone_id; ?>');
	//--></script> 
    </form>
  </div>
</div>
