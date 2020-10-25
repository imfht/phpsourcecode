  <div class="box">
    <div class="heading">
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons"><a onclick="$('#form').submit();" class="btn btn-primary"><span><?php echo $button_save; ?></span></a> <a onclick="location = '<?php echo $cancel; ?>';" class="btn"><span><?php echo $button_cancel; ?></span></a></div>
    </div>
    <div class="content">
      <div class="vtabs"><a href="#tab-order"><?php echo $tab_order; ?></a><a href="#tab-product"><?php echo $tab_product; ?></a><a href="#tab-shipping"><?php echo $tab_shipping; ?></a><a href="#tab-total"><?php echo $tab_total; ?></a></div>
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
        <div id="tab-order" class="vtabs-content">
          <table class="form">
            <tr>
              <td><?php echo $entry_store; ?></td>
              <td><select name="store_id">
                  <option value="0"><?php echo $text_default; ?></option>
                  <?php foreach ($stores as $store) { ?>
                  <?php if ($store['store_id'] == $store_id) { ?>
                  <option value="<?php echo $store['store_id']; ?>" selected="selected"><?php echo $store['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $store['store_id']; ?>"><?php echo $store['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_customer; ?></td>
              <td><input type="text" name="customer" value="<?php echo $customer; ?>" />
                <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
              <td><input type="text" name="firstname" value="<?php echo $firstname; ?>" />
                <?php if ($error_firstname) { ?>
                <span class="error"><?php echo $error_firstname; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_lastname; ?></td>
              <td><input type="text" name="lastname" value="<?php echo $lastname; ?>" />
                <?php if ($error_lastname) { ?>
                <span class="error"><?php echo $error_lastname; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_email; ?></td>
              <td><input type="text" name="email" value="<?php echo $email; ?>" />
                <?php if ($error_email) { ?>
                <span class="error"><?php echo $error_email; ?></span>
                <?php  } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_telephone; ?></td>
              <td><input type="text" name="telephone" value="<?php echo $telephone; ?>" />
                <?php if ($error_telephone) { ?>
                <span class="error"><?php echo $error_telephone; ?></span>
                <?php  } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_fax; ?></td>
              <td><input type="text" name="fax" value="<?php echo $fax; ?>" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_order_status; ?></td>
              <td><select name="order_status_id">
                  <?php foreach ($order_statuses as $order_status) { ?>
                  <?php if ($order_status['order_status_id'] == $order_status_id) { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><?php echo $entry_comment; ?></td>
              <td><textarea name="comment" cols="40" rows="5"><?php echo $comment; ?></textarea></td>
            </tr>
            <tr>
              <td><?php echo $entry_affiliate; ?></td>
              <td><input type="text" name="affiliate" value="<?php echo $affiliate; ?>" />
                <input type="hidden" name="affiliate_id" value="<?php echo $affiliate_id; ?>" /></td>
            </tr>
          </table>
        </div>
        <div id="tab-product" class="vtabs-content">
          <table id="product" class="list">
            <thead>
              <tr>
                <td class="left"><?php echo $entry_product; ?></td>
                <td class="left"><?php echo $entry_model; ?></td>
                <td class="right"><?php echo $entry_quantity; ?></td>
                <td class="right"><?php echo $entry_price; ?></td>
                <td></td>
              </tr>
            </thead>
            <?php $product_row = 0; ?>
            <?php foreach ($order_products as $order_product) { ?>
            <tbody id="product-row<?php echo $product_row; ?>">
              <tr>
                <td class="left"><input type="text" name="order_product[<?php echo $product_row; ?>][name]" value="<?php echo $order_product['name']; ?>" />
                  <input type="hidden" name="order_product[<?php echo $product_row; ?>][order_product_id]" value="<?php echo $order_product['order_product_id']; ?>" />
                  <input type="hidden" name="order_product[<?php echo $product_row; ?>][product_id]" value="<?php echo $order_product['product_id']; ?>" />
                  <br />
                  <?php $option_row = 0; ?>
                  <?php foreach ($order_product['option'] as $option) { ?>
                  <?php if ($option['type'] == 'select') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <select name="order_product[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][option_value_id]">
                    <option value=""><?php echo $text_select; ?></option>
                    <?php foreach ($option['option_value'] as $option_value) { ?>
                    <option value="<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                    </option>
                    <?php } ?>
                  </select>
                  <br />
                  <?php } ?>
                  <?php if ($option['type'] == 'radio') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <?php foreach ($option['option_value'] as $option_value) { ?>
                  <input type="radio" name="option[<?php echo $option['product_option_id']; ?>]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                  <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                  </label>
                  <br />
                  <?php } ?>
                  <?php } ?>
                  <?php if ($option['type'] == 'checkbox') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <?php foreach ($option['option_value'] as $option_value) { ?>
                  <input type="checkbox" name="option[<?php echo $option['product_option_id']; ?>][]" value="<?php echo $option_value['product_option_value_id']; ?>" id="option-value-<?php echo $option_value['product_option_value_id']; ?>" />
                  <label for="option-value-<?php echo $option_value['product_option_value_id']; ?>"><?php echo $option_value['name']; ?>
                    <?php if ($option_value['price']) { ?>
                    (<?php echo $option_value['price_prefix']; ?><?php echo $option_value['price']; ?>)
                    <?php } ?>
                  </label>
                  <br />
                  <?php } ?>
                  <?php } ?>
                  <?php if ($option['type'] == 'text') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <input type="text" name="order_product[<?php echo $product_row; ?>][<?php echo $option_row; ?>][option_value]" value="<?php echo $option['option_value']; ?>" />
                  <br />
                  <?php } ?>
                  <?php if ($option['type'] == 'textarea') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <textarea name="order_product[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][option_value]" cols="40" rows="5"><?php echo $option['option_value']; ?></textarea>
                  <br />
                  <?php } ?>
                  <?php if ($option['type'] == 'file') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <input type="text" name="order_product[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][option_value]" value="<?php echo $option['option_value']; ?>" />
                  <br />
                  <?php } ?>
                  <?php if ($option['type'] == 'date') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <input type="text" name="order_product[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][option_value]" value="<?php echo $option['option_value']; ?>" class="date" />
                  <br />
                  <?php } ?>
                  <?php if ($option['type'] == 'datetime') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <input type="text" name="order_product[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][option_value]" value="<?php echo $option['option_value']; ?>" class="datetime" />
                  <br />
                  <?php } ?>
                  <?php if ($option['type'] == 'time') { ?>
                  <?php if ($option['required']) { ?>
                  <span class="required">*</span>
                  <?php } ?>
                  <?php echo $option['name']; ?><br />
                  <input type="text" name="order_product[<?php echo $product_row; ?>][option][<?php echo $option_row; ?>][option_value]" value="<?php echo $option['option_value']; ?>" class="time" />
                  <br />
                  <?php } ?>
                  <?php $option_row++; ?>
                  <?php } ?></td>
                <td class="left"><input type="text" name="order_product[<?php echo $product_row; ?>][model]" value="<?php echo $order_product['model']; ?>" /></td>
                <td class="right"><input type="text" name="order_product[<?php echo $product_row; ?>][quantity]" value="<?php echo $order_product['quantity']; ?>" size="3" /></td>
                <td class="right"><input type="text" name="order_product[<?php echo $product_row; ?>][price]" value="<?php echo $order_product['price']; ?>" size="4" /></td>
                <td class="left"><a onclick="$('#product-row<?php echo $product_row; ?>').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>
              </tr>
            </tbody>
            <?php $product_row++; ?>
            <?php } ?>
            <tfoot>
              <tr>
                <td colspan="4"></td>
                <td class="left"><a onclick="addProduct();" class="button"><span><?php echo $button_add_product; ?></span></a></td>
              </tr>
            </tfoot>
          </table>
        </div>
  		<div id="tab-shipping" class="vtabs-content">
          <table class="form">
            <tr>
              <td><?php echo $entry_address; ?></td>
              <td><select name="shipping_address">
                  <option value="0"><?php echo $text_none; ?></option>
                  <?php foreach ($addresses as $address) { ?>
                  <option value="<?php echo $address['address_id']; ?>"><?php echo $address['firstname'] . ' ' . $address['lastname'] . ', ' . $address['address_1'] . ', ' . $address['city'] . ', ' . $address['country']; ?></option>
                  <?php } ?>
                </select></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_firstname; ?></td>
              <td><input type="text" name="shipping_firstname" value="<?php echo $shipping_firstname; ?>" />
                <?php if ($error_shipping_firstname) { ?>
                <span class="error"><?php echo $error_shipping_firstname; ?></span>
                <?php } ?></td>
            </tr>
        	 <tr>
              <td><?php echo $entry_company; ?></td>
              <td><input type="text" name="shipping_company" value="<?php echo $shipping_company; ?>" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_address_1; ?></td>
              <td><input type="text" name="shipping_address_1" value="<?php echo $shipping_address_1; ?>" />
                <?php if ($error_shipping_address_1) { ?>
                <span class="error"><?php echo $error_shipping_address_1; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_address_2; ?></td>
              <td><input type="text" name="shipping_address_2" value="<?php echo $shipping_address_2; ?>" /></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_city; ?></td>
              <td><input type="text" name="shipping_city" value="<?php echo $shipping_city; ?>" />
                <?php if ($error_shipping_city) { ?>
                <span class="error"><?php echo $error_shipping_city; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_postcode; ?></td>
              <td><input type="text" name="shipping_postcode" value="<?php echo $shipping_postcode; ?>" />
                <?php if ($error_shipping_postcode) { ?>
                <span class="error"><?php echo $error_shipping_postcode; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_country; ?></td>
              <td><select name="shipping_country_id" onchange="$('select[name=\'shipping_zone_id\']').load('index.php?route=sale/customer/zone&token=<?php echo $token; ?>&country_id=' + this.value + '&zone_id=<?php echo $shipping_zone_id; ?>&m=<?php echo SNAME; ?>');">
                  <option value="false"><?php echo $text_select; ?></option>
                  <?php foreach ($countries as $country) { ?>
                  <?php if ($country['country_id'] == $shipping_country_id) { ?>
                  <option value="<?php echo $country['country_id']; ?>" selected="selected"><?php echo $country['name']; ?></option>
                  <?php } else { ?>
                  <option value="<?php echo $country['country_id']; ?>"><?php echo $country['name']; ?></option>
                  <?php } ?>
                  <?php } ?>
                </select>
                <?php if ($error_shipping_country) { ?>
                <span class="error"><?php echo $error_shipping_country; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><span class="required">*</span> <?php echo $entry_zone; ?></td>
              <td><select name="shipping_zone_id">
                </select>
                <?php if ($error_shipping_zone) { ?>
                <span class="error"><?php echo $error_shipping_zone; ?></span>
                <?php } ?></td>
            </tr>
            <tr>
              <td><?php echo $entry_shipping; ?></td>
              <td><input type="text" name="shipping_method" value="<?php echo $shipping_method; ?>" /></td>
            </tr>
            <tr>
              <td><?php echo $entry_payment; ?></td>
              <td><input type="text" name="payment_method" value="<?php echo $payment_method; ?>" /></td>
            </tr>
          </table>
        </div>
        <div id="tab-total" class="vtabs-content">
    
          <table class="list" id="total">
            <thead>
              <tr>
                <td class="right"><?php echo $text_total_title;?></td>
                <td class="right"><?php echo $text_amount;?></td>
                <td class="right"><?php echo $text_sort_order;?></td>
                <td></td>
              </tr>
            </thead>
            <?php $total_row = 0; ?>
            <?php foreach ($order_totals as $order_total) { ?>
            <tbody id="total-row<?php echo $total_row; ?>">
              <tr>
                <td class="right"><input type="hidden" name="order_total[<?php echo $total_row; ?>][order_total_id]" value="<?php echo $order_total['order_total_id']; ?>" />
                  <input type="text" name="order_total[<?php echo $total_row; ?>][title]" value="<?php echo $order_total['title']; ?>" /></td>
                <td class="right"><input type="hidden" name="order_total[<?php echo $total_row; ?>][text]" value="<?php echo $order_total['text']; ?>" />
                  <input type="text" name="order_total[<?php echo $total_row; ?>][value]" value="<?php echo $order_total['value']; ?>" /></td>
                <td class="right"><input type="text" name="order_total[<?php echo $total_row; ?>][sort_order]" value="<?php echo $order_total['sort_order']; ?>" /></td>
                <td class="left"><a onclick="$('#total-row<?php echo $product_row; ?>').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>
              </tr>
            </tbody>
            <?php $total_row++; ?>
            <?php } ?>
            <tfoot>
              <tr>
                <td colspan="3"></td>
                <td class="left"><a onclick="addTotal();" class="button"><span><?php echo $button_insert;?></span></a></td>
              </tr>
              <tr>
                <td colspan="3"></td>
                <td class="left"><a onclick="calculate();" class="button"><span><?php echo $text_calculate;?></span></a></td>
              </tr>
            </tfoot>
          </table>
        </div>
      </form>
    </div>
  </div>

<script type="text/javascript"><!--
$.widget('custom.catcomplete', $.ui.autocomplete, {
	_renderMenu: function(ul, items) {
		var self = this, currentCategory = '';
		
		$.each(items, function(index, item) {
			if (item.category != currentCategory) {
				ul.append('<li class="ui-autocomplete-category">' + item.category + '</li>');
				
				currentCategory = item.category;
			}
			
			self._renderItem(ul, item);
		});
	}
});

$('input[name=\'customer\']').catcomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/customer/autocomplete&token=<?php echo $token; ?>&m=<?php echo SNAME; ?>',
			type: 'POST',
			dataType: 'json',
			data: 'filter_name=' +  encodeURIComponent(request.term),
			success: function(data) {	
				response($.map(data, function(item) {
					return {
						category: item.customer_group,
						label: item.name,
						value: item.customer_id,
						customer_group_id: item.customer_group_id,
						firstname: item.firstname,
						lastname: item.lastname,
						email: item.email,
						telephone: item.telephone,
						fax: item.fax,
						address: item.address
					}
				}));
			}
		});
	}, 
	select: function(event, ui) { 
		$('input[name=\'customer\']').attr('value', ui.item.label);
		$('input[name=\'customer_id\']').attr('value', ui.item.value);
		$('input[name=\'firstname\']').attr('value', ui.item.firstname);
		$('input[name=\'lastname\']').attr('value', ui.item.lastname);
		$('input[name=\'email\']').attr('value', ui.item.email);
		$('input[name=\'telephone\']').attr('value', ui.item.telephone);
		$('input[name=\'fax\']').attr('value', ui.item.fax);
			
		html = '<option value="0"><?php echo $text_none; ?></option>'; 
			
		for (i = 0; i < ui.item.address.length; i++) {
			html += '<option value="' + ui.item.address[i].address_id + '">' + ui.item.address[i].firstname + ' ' + ui.item.address[i].lastname + ', ' + ui.item.address[i].address_1 + ', ' + ui.item.address[i].city + ', ' + ui.item.address[i].country + '</option>';
		}
		
		$('select[name=\'shipping_address\']').html(html);
		$('select[name=\'payment_address\']').html(html);
			
		return false; 
	}
});

$('select[name=\'shipping_address\']').bind('change', function() {
	$.ajax({
		url: 'index.php?route=sale/customer/address&token=<?php echo $token; ?>&m=<?php echo SNAME; ?>',
		type: 'POST',
		dataType: 'json',
		data: 'address_id=' +  this.value,
		success: function(data) {
			if (data) {	
				$('input[name=\'shipping_firstname\']').attr('value', data.firstname);
				$('input[name=\'shipping_company\']').attr('value', data.company);
				$('input[name=\'shipping_address_1\']').attr('value', data.address_1);
				$('input[name=\'shipping_address_2\']').attr('value', data.address_2);
				$('input[name=\'shipping_city\']').attr('value', data.city);
				$('input[name=\'shipping_postcode\']').attr('value', data.postcode);
				$('select[name=\'shipping_country_id\']').attr('value', data.country_id);
				$('select[name=\'shipping_zone_id\']').load('index.php?route=sale/order/zone&token=<?php echo $token; ?>&country_id=' + data.country_id + '&zone_id=' + data.zone_id+'&m=<?php echo SNAME; ?>');
			}
		}
	});	
});

$('select[name=\'shipping_zone_id\']').load('index.php?route=sale/order/zone&token=<?php echo $token; ?>&country_id=<?php echo $shipping_country_id; ?>&zone_id=<?php echo $shipping_zone_id; ?>&m=<?php echo SNAME; ?>');

//--></script> 
<script type="text/javascript"><!--
var product_row = <?php echo $product_row; ?>;

function addProduct() {
    html  = '<tbody id="product-row' + product_row + '">';
    html += '  <tr>';
    html += '    <td class="left"><input type="text" name="order_product[' + product_row + '][name]" value="" /><input type="hidden" name="order_product[' + product_row + '][order_product_id]" value="" /><input type="hidden" name="order_product[' + product_row + '][product_id]" value="" /></td>';
    html += '    <td class="left"><input type="text" name="order_product[' + product_row + '][model]" value="" /></td>';
	html += '    <td class="right"><input type="text" name="order_product[' + product_row + '][quantity]" value="1" size="3" /></td>';	
	html += '    <td class="right"><input type="text" name="order_product[' + product_row + '][price]" value="" size="4" /></td>';
    html += '    <td class="left"><a onclick="$(\'#product-row' + product_row + '\').remove();" class="button"><span><?php echo $button_remove; ?></span></a></td>';
    html += '  </tr>';
	html += '</tbody>';
	
	$('#product tfoot').before(html);

	productautocomplete(product_row);
	
	product_row++;
}

function productautocomplete(product_row) {
	$('input[name=\'order_product[' + product_row + '][name]\']').autocomplete({
		delay: 0,
		source: function(request, response) {
			$.ajax({
				url: 'index.php?route=catalog/product/autocomplete&token=<?php echo $token; ?>&m=<?php echo SNAME; ?>',
				type: 'POST',
				dataType: 'json',
				data: 'filter_name=' +  encodeURIComponent(request.term),
				success: function(data) {	
					response($.map(data, function(item) {
						return {
							label: item.name,
							value: item.product_id,
							model: item.model,
							price: item.price
						}
					}));
				}
			});
		}, 
		select: function(event, ui) {
			$('input[name=\'order_product[' + product_row + '][product_id]\']').attr('value', ui.item.value);
			$('input[name=\'order_product[' + product_row + '][name]\']').attr('value', ui.item.label);
			$('input[name=\'order_product[' + product_row + '][model]\']').attr('value', ui.item.model);
			$('input[name=\'order_product[' + product_row + '][price]\']').attr('value', ui.item.price);
			
			return false;
		}
	});
}

$('#product tbody').each(function(index, element) {
	productautocomplete(index);
});		
//--></script> 
<script type="text/javascript"><!--
$('input[name=\'affiliate\']').autocomplete({
	delay: 0,
	source: function(request, response) {
		$.ajax({
			url: 'index.php?route=sale/affiliate/autocomplete&token=<?php echo $token; ?>&m=<?php echo SNAME; ?>',
			type: 'POST',
			dataType: 'json',
			data: 'filter_name=' +  encodeURIComponent(request.term),
			success: function(data) {	
				response($.map(data, function(item) {
					return {
						label: item.name,
						value: item.affiliate_id,
					}
				}));
			}
		});
	}, 
	select: function(event, ui) { 
		$('input[name=\'affiliate\']').attr('value', ui.item.label);
		$('input[name=\'affiliate_id\']').attr('value', ui.item.value);
			
		return false; 
	}
});


//--></script> 
<script type="text/javascript" src="view/javascript/jquery/ui/jquery-ui-timepicker-addon.js"></script> 
<script type="text/javascript" src="view/javascript/jquery/ui/i18n/jquery-ui-i18n.js"></script>
<script type="text/javascript"><!--
$('.date').datepicker({dateFormat: 'yy-mm-dd'});
$('.datetime').datetimepicker({
	dateFormat: 'yy-mm-dd',
	timeFormat: 'h:m'
});
$('.time').timepicker({timeFormat: 'h:m'});
//--></script> 
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script> 
