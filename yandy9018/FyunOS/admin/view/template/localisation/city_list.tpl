 <?php if ($error_warning) { ?>
  <div class="alert alert-error"><?php echo $error_warning; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <?php if ($success) { ?>
  <div class="alert alert-success"><?php echo $success; ?><a class="close" data-dismiss="alert">×</a></div>
  <?php } ?>
  <div class="box">
    <div class="heading">
      <h2><?php echo $heading_title; ?></h2>
      <div class="buttons"><button onclick="location = '<?php echo $insert; ?>'" class="btn btn-primary"><?php echo $button_insert; ?></button> <button onclick="$('form').submit();" class="btn"><?php echo $button_delete; ?></button></div>
    </div>
  <div class="content">
    <form action="<?php echo $delete;?>" method="post" enctype="multipart/form-data" id="form">
      <table class="list">
        <thead>
          <tr>
            <td width="1" style="text-align: center;"><input type="checkbox" onclick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></td>
            <td class="left"><?php if ($sort == 'city_zone') { ?>
              <a href="<?php echo $sort_zone; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_zone; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_zone; ?>"><?php echo $column_zone; ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'city_name') { ?>
              <a href="<?php echo $sort_name; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_name; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_name; ?>"><?php echo $column_name; ?></a>
              <?php } ?></td>    
            <td class="left"><?php if ($sort == 'city_country') { ?>
              <a href="<?php echo $sort_country; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_country; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_country; ?>"><?php echo $column_country; ?></a>
              <?php } ?></td>
            <td class="left"><?php if ($sort == 'city_status') { ?>
              <a href="<?php echo $sort_status; ?>" class="<?php echo strtolower($order); ?>"><?php echo $column_status; ?></a>
              <?php } else { ?>
              <a href="<?php echo $sort_status; ?>"><?php echo $column_status; ?></a>
              <?php } ?></td>              
            <td class="right"><?php echo $column_action; ?></td>
          </tr>
        </thead>
        <tbody>
          <tr class="filter">
            <td></td>
            <td><input type="text" name="filter_zone" class="span2"  value="<?php echo $filter_zone; ?>" /></td>
            <td><input type="text" name="filter_city"  class="span2"  value="<?php echo $filter_city; ?>" /></td>
	  
            <td><select name="filter_country_id" class="span2" >
                <option value="*"></option>
                <?php foreach ($countries as $country) { ?>
                <?php if ($country['country_id'] == $filter_country_id) { ?>
                <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
            <td><select name="filter_status" class="span2" >
                <option value="*"></option>
                <?php if ($filter_status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <?php } ?>
                <?php if (!is_null($filter_status) && !$filter_status) { ?>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select></td>
            <td align="right" nowrap><div class="buttons"><a onclick="filter();" class="button"><?php echo $button_filter; ?></a></div></td>
          </tr>
          <?php if ($cities) { ?>
          <?php foreach ($cities as $city) { ?>
          <tr>
            <td style="text-align: center;"><?php if ($city['selected']) { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $city['city_id']; ?>" checked="checked" />
              <?php } else { ?>
              <input type="checkbox" name="selected[]" value="<?php echo $city['city_id']; ?>" />
              <?php } ?></td>
            <td class="left"><?php echo $city['zone']; ?></td>
            <td class="left"><?php echo $city['name']; ?></td>
            <td class="left"><?php echo $city['country']; ?></td>
            <td class="left"><?php echo $city['status']; ?></td>
            <td class="right"><?php foreach ($city['action'] as $action) { ?>
              [ <a href="<?php echo $action['href']; ?>"><?php echo $action['text']; ?></a> ]
              <?php } ?></td>
          </tr>
          <?php } ?>
          <?php } else { ?>
          <tr>
            <td class="center" colspan="7"><?php echo $text_no_results; ?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </form>
    <div class="pagination"><?php echo $pagination; ?></div>
  </div>
</div>
<script type="text/javascript"><!--
function filter() {
	url = 'index.php?route=localisation/city&token=<?php echo $token; ?>';
	
	var filter_zone = $('input[name=\'filter_zone\']').attr('value');
	
	if (filter_zone) {
		url += '&filter_zone=' + encodeURIComponent(filter_zone);
	}
	
	var filter_city = $('input[name=\'filter_city\']').attr('value');
	
	if (filter_city) {
		url += '&filter_city=' + encodeURIComponent(filter_city);
	}

	var filter_code = $('input[name=\'filter_code\']').attr('value');
	
	if (filter_code) {
		url += '&filter_code=' + encodeURIComponent(filter_code);
	}
	
	var filter_country_id = $('select[name=\'filter_country_id\']').attr('value');
	
	if (filter_country_id != '*') {
		url += '&filter_country_id=' + encodeURIComponent(filter_country_id);
	}	
	
	var filter_status = $('select[name=\'filter_status\']').attr('value');
	
	if (filter_status != '*') {
		url += '&filter_status=' + encodeURIComponent(filter_status); 
	}	
		
	location = url;
}
//--></script>