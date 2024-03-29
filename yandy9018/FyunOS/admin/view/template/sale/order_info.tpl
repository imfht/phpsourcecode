<div class="box">
    <div class="heading">
      <h1><img src="view/image/order.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons" style="margin-bottom:10px;"><button onclick="window.open('<?php echo $invoice; ?>');" class="btn btn-primary" ><?php echo $button_invoice; ?></button> <button onclick="location = '<?php echo $cancel; ?>';" class="btn"><?php echo $button_cancel; ?></button></div>
    </div>
    <div class="content">
      <div class="vtabs"><a href="#tab-order"><?php echo $tab_order; ?></a>
        <?php if ($vtype==2) { ?>
        <a href="#tab-shipping"><?php echo $tab_shipping; ?></a>
        <?php } ?>
        <a href="#tab-product"><?php echo $tab_product; ?></a>
        <a href="#tab-history"><?php echo $tab_order_history; ?></a>
     </div>
   
      <div id="tab-order" class="vtabs-content" >
        <table class="form">
        <tr>
            <td><b><?php echo $text_type; ?></b></td>
            <td><b><?php echo $type; ?></b><?php if ($vtype==1) { ?>( <?php echo $seat; ?> ) <?php } ?></td>
          </tr>
          <tr>
          
            <td><?php echo $text_order_id; ?></td>
            <td>#<?php echo $order_id; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_invoice_no; ?></td>
            <td><?php echo $invoice_no; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_store_name; ?></td>
            <td><?php echo $store_name; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_store_url; ?></td>
            <td><a onclick="window.open('<?php echo $store_url; ?>');"><u><?php echo $store_url; ?></u></a></td>
          </tr>
          <?php if ($customer) { ?>
          <tr>
            <td><?php echo $text_customer; ?></td>
            <td><a href="<?php echo $customer; ?>"><?php echo $firstname; ?> </a></td>
          </tr>
          <?php } else { ?>
          <tr>
            <td><?php echo $text_customer; ?></td>
            <td><?php echo $firstname; ?> </td>
          </tr>
          <?php } ?>
          <?php if ($customer_group) { ?>
          <tr>
            <td><?php echo $text_customer_group; ?></td>
            <td><?php echo $customer_group; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_ip; ?></td>
            <td><?php echo $ip; ?></td>
          </tr>
          
          <tr>
            <td><?php echo $text_telephone; ?></td>
            <td><?php echo $telephone; ?></td>
          </tr>
          <?php if ($fax) { ?>
          <tr>
            <td><?php echo $text_fax; ?></td>
            <td><?php echo $fax; ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_total; ?></td>
            <td><?php echo $total; ?>
              <?php if ($credit && $customer) { ?>
              <?php if (!$credit_total) { ?>
              <img src="view/image/add.png" alt="<?php echo $text_credit_add; ?>" title="<?php echo $text_credit_add; ?>" id="credit_add" class="icon" />
              <?php } else { ?>
              <img src="view/image/delete.png" alt="<?php echo $text_credit_remove; ?>" title="<?php echo $text_credit_remove; ?>" id="credit_remove" class="icon" />
              <?php } ?>
              <?php } ?></td>
          </tr>
          <?php if ($reward && $customer) { ?>
          <tr>
            <td><?php echo $text_reward; ?></td>
            <td><?php echo $reward; ?>
              <?php if (!$reward_total) { ?>
              <img src="view/image/add.png" alt="<?php echo $text_reward_add; ?>" title="<?php echo $text_reward_add; ?>" id="reward_add" class="icon" />
              <?php } else { ?>
              <img src="view/image/delete.png" alt="<?php echo $text_reward_remove; ?>" title="<?php echo $text_reward_remove; ?>" id="reward_remove" class="icon" />
              <?php } ?></td>
          </tr>
          <?php } ?>
          <?php if ($order_status) { ?>
          <tr>
            <td><?php echo $text_order_status; ?></td>
            <td id="order-status"><?php echo $order_status; ?></td>
          </tr>
          <?php } ?>
          <?php if ($comment) { ?>
          <tr>
            <td><?php echo $text_comment; ?></td>
            <td><?php echo $comment; ?></td>
          </tr>
          <?php } ?>
          <?php if ($affiliate) { ?>
          <tr>
            <td><?php echo $text_affiliate; ?></td>
            <td><a href="<?php echo $affiliate; ?>"><?php echo $affiliate_firstname; ?> <?php echo $affiliate_lastname; ?></a></td>
          </tr>
          <tr>
            <td><?php echo $text_commission; ?></td>
            <td><?php echo $commission; ?>
              <?php if (!$commission_total) { ?>
              <img src="view/image/add.png" alt="<?php echo $text_commission_add; ?>" title="<?php echo $text_commission_add; ?>" id="commission_add" class="icon" />
              <?php } else { ?>
              <img src="view/image/delete.png" alt="<?php echo $text_commission_remove; ?>" title="<?php echo $text_commission_remove; ?>" id="commission_remove" class="icon" />
              <?php } ?></td>
          </tr>
          <?php } ?>
          <tr>
            <td><?php echo $text_date_added; ?></td>
            <td><?php echo $date_added; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_date_modified; ?></td>
            <td><?php echo $date_modified; ?></td>
          </tr>
        </table>
      </div>
    
      <?php if ($vtype==2) { ?>
      <div id="tab-shipping" class="vtabs-content">
        <table class="form">
          <tr>
            <td><?php echo $text_firstname; ?></td>
            <td><?php echo $firstname; ?></td>
          </tr>
         
         <tr>
            <td><?php echo $text_phone; ?></td>
            <td><?php echo $telephone; ?></td>
         </tr>
          <tr>
            <td><?php echo $text_zone; ?></td>
            <td><?php echo $zone_name; ?></td>
          </tr>
        
            <tr>
            <td><?php echo $text_city; ?></td>
            <td><?php echo $city_name; ?></td>
          </tr>
          <tr>
            <td><?php echo $text_address_1; ?></td>
            <td><?php echo $address; ?></td>
          </tr>
        </table>
      </div>
      <?php } ?>
      <div id="tab-product" class="vtabs-content">
        <table id="product" class="list">
          <thead>
            <tr>
              <td class="left"><?php echo $column_product; ?></td>
              <td class="right"><?php echo $column_quantity; ?></td>
              <td class="right"><?php echo $column_price; ?></td>
              <td class="right"><?php echo $column_total; ?></td>
            </tr>
          </thead>
          <?php foreach ($products as $product) { ?>
          <tbody id="product-row<?php echo $product['order_product_id']; ?>">
            <tr>
              <td class="left"><?php if ($product['product_id']) { ?>
                <a href="<?php echo $product['href']; ?>"><?php echo $product['name']; ?></a>
                <?php } else { ?>
                <?php echo $product['name']; ?>
                <?php } ?>
                <?php foreach ($product['option'] as $option) { ?>
                <br />
                <?php if ($option['type'] != 'file') { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
                <?php } else { ?>
                &nbsp;<small> - <?php echo $option['name']; ?>: <a href="<?php echo $option['href']; ?>"><?php echo $option['value']; ?></a></small>
                <?php } ?>
                <?php } ?></td>
              <td class="right"><?php echo $product['quantity']; ?></td>
              <td class="right"><?php echo $product['price']; ?></td>
              <td class="right"><?php echo $product['total']; ?></td>
            </tr>
          </tbody>
          <?php } ?>
          <?php foreach ($totals as $totals) { ?>
          <tbody id="totals">
            <tr>
              <td colspan="3" class="right"><?php echo $totals['title']; ?>:</td>
              <td class="right"><?php echo $totals['text']; ?></td>
            </tr>
          </tbody>
          <?php } ?>
        </table>
      </div>
      <div id="tab-history" class="vtabs-content">
        <div id="history"></div>
       	<table class="form">

        	<tr>
            <td><?php echo $text_express;?></td>
            <td><select id="express"  disabled="false" name="express">
            	<option value=""><?php echo $text_select; ?></option>
                <?php foreach ($expresses as $express) { ?>
               		<option value="<?php echo $express['logistics_id']; ?>"><?php echo $express['logistics_name']; ?></option>
                <?php } ?>
               </select>
             </td>
          </tr>
      
          
          <tr>
            <td><?php echo $entry_order_status; ?></td>
            <td><select name="order_status_id" onChange="changefee(this.options[this.options.selectedIndex].value)">
                <?php foreach ($order_statuses as $order_statuses) { ?>
                <?php if ($order_statuses['order_status_id'] == $order_status_id) { ?>
                <option value="<?php echo $order_statuses['order_status_id']; ?>" selected="selected"><?php echo $order_statuses['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_statuses['order_status_id']; ?>"><?php echo $order_statuses['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select></td>
          </tr>
          <tr>
            <td><?php echo $entry_notify; ?></td>
            <td><input type="checkbox" name="notify" value="1" /></td>
          </tr>
          <tr>
            <td><?php echo $entry_comment; ?></td>
            <td><textarea name="comment" cols="40" rows="8" style="width: 99%"></textarea>
              <div style="margin-top: 10px; text-align: left;"><a onclick="history();" id="button-history" class="btn"><span><?php echo $button_add_history; ?></span></a></div></td>
          </tr>
        </table>
      </div>

    </div>
  </div>

<script type="text/javascript"><!--
function changefee(status_id){ 
if(status_id == 2){
	document.getElementById("express").disabled=false;
	}else{
		document.getElementById("express").disabled=true;
		}
}
$('#reward_add').live('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/order/addreward&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>',
		dataType: 'json',
		success: function(json) {
			if (json.error) {
				alert(json.error);
			}
			
			if (json.success) {
				alert(json.success);

				$('#reward_add').fadeOut();
                
				$('#reward_add').replaceWith('<img src="view/image/delete.png" alt="<?php echo $text_reward_remove; ?>" id="reward_remove" class="icon" />');
      		  			
				$('#reward_remove').fadeIn();
			}
		}
	});
});

$('#reward_remove').live('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/order/removereward&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>',
		dataType: 'json',
		success: function(json) {
			if (json.error) {
				alert(json.error);
			}
			
			if (json.success) {
				alert(json.success);
				
				$('#reward_remove').fadeOut();
				
				$('#reward_remove').replaceWith('<img src="view/image/add.png" alt="<?php echo $text_reward_add; ?>" id="reward_add" class="icon" />');
				
				$('#reward_add').fadeIn();
			}
		}
	});
});

$('#commission_add').live('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/order/addcommission&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>',
		dataType: 'json',
		success: function(json) {
			if (json.error) {
				alert(json.error);
			}
			
			if (json.success) {
				alert(json.success);

				$('#commission_add').fadeOut();
                
				$('#commission_add').replaceWith('<img src="view/image/delete.png" alt="<?php echo $text_commission_remove; ?>" id="commission_remove" class="icon" />');
      		  			
				$('#commission_remove').fadeIn();
			}
		}
	});
});

$('#commission_remove').live('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/order/removecommission&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>',
		dataType: 'json',
		success: function(json) {
			if (json.error) {
				alert(json.error);
			}
			
			if (json.success) {
				alert(json.success);
				
				$('#commission_remove').fadeOut();
				
				$('#commission_remove').replaceWith('<img src="view/image/add.png" alt="<?php echo $text_commission_add; ?>" id="commission_add" class="icon" />');
				
				$('#commission_add').fadeIn();
			}
		}
	});
});

$('#credit_add').live('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/order/addcredit&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>',
		dataType: 'json',
		success: function(json) {
			if (json.error) {
				alert(json.error);
			}
			
			if (json.success) {
				alert(json.success);

				$('#credit_add').fadeOut();
                
				$('#credit_add').replaceWith('<img src="view/image/delete.png" alt="<?php echo $text_credit_remove; ?>" id="credit_remove" class="icon" />');
      		  			
				$('#credit_remove').fadeIn();
			}
		}
	});
});

$('#credit_remove').live('click', function() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/order/removecredit&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>',
		dataType: 'json',
		success: function(json) {
			if (json.error) {
				alert(json.error);
			}
			
			if (json.success) {
				alert(json.success);
				
				$('#credit_remove').fadeOut();
				
				$('#credit_remove').replaceWith('<img src="view/image/add.png" alt="<?php echo $text_credit_add; ?>" id="credit_add" class="icon" />');
				
				$('#credit_add').fadeIn();
			}
		}
	});
});

$('#history .pagination a').live('click', function() {
	$('#history').load(this.href);
	
	return false;
});			

$('#history').load('index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>');

function history() {
	$.ajax({
		type: 'POST',
		url: 'index.php?route=sale/order/history&token=<?php echo $token; ?>&order_id=<?php echo $order_id; ?>&m=<?php echo SNAME; ?>',
		dataType: 'html',
		data: 'order_status_id=' + encodeURIComponent($('select[name=\'order_status_id\']').val()) +'&express=' + encodeURIComponent($('select[name=\'express\']').val()) + '&notify=' + encodeURIComponent($('input[name=\'notify\']').attr('checked') ? 1 : 0) + '&append=' + encodeURIComponent($('input[name=\'append\']').attr('checked') ? 1 : 0) + '&comment=' + encodeURIComponent($('textarea[name=\'comment\']').val()),
		beforeSend: function() {
			$('.success, .warning').remove();
			$('#button-history').attr('disabled', true);
			$('#history').prepend('<div class="attention"><img src="view/image/loading.gif" alt="" /> <?php echo $text_wait; ?></div>');
		},
		complete: function() {
			$('#button-history').attr('disabled', false);
			$('.attention').remove();
		},
		success: function(html) {
			$('#history').html(html);
			
			$('textarea[name=\'comment\']').val('');
			
			$('#order-status').html($('select[name=\'order_status_id\'] option:selected').text());
		}
	});
}
//--></script> 
<script type="text/javascript"><!--
$('.vtabs a').tabs();
//--></script> 
