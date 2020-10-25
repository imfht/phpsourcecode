<?php echo $header; ?><?php echo $column_left; ?><?php echo $column_right; ?>
<div id="content"><?php echo $content_top; ?>
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
  <h1><?php echo $heading_title; ?></h1>
  <?php if ($error_warning) { ?>
  <div class="warning"><?php echo $error_warning; ?></div>
  <?php } ?>
  <?php if($payment!=''){?>
  <div class="right">
  		<?php echo $payment;?>
  </div>
  <?php } ?>
  <table class="list">
    <thead>
      <tr>
        <td class="left" colspan="2"><?php echo $text_order_detail; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left" style="width: 50%;">
          <b><?php echo $text_order_id; ?></b> #<?php echo $order_id; ?><br />
          <b><?php echo $text_date_added; ?></b> <?php echo $date_added; ?></td>
        <td class="left"><b><?php echo $text_payment_method; ?></b> <?php echo $payment_method; ?><br />
          <?php if ($shipping_method) { ?>
          <b><?php echo $text_shipping_method; ?></b> <?php echo $shipping_method; ?>
          <?php } ?></td>
      </tr>
    </tbody>
  </table>
  <?php if ($shipping_required) { ?>
  <table class="list">
    <thead>
      <tr>
       <td class="left"><?php echo $text_shipping_address; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
       	<td class="left"><?php echo $shipping_address; ?></td>
      </tr>
      
	</tbody>
  </table>
  <?php if(isset($express)&&$express){?>
	  <table class="list">
	  <thead>
	  <tr>
	  <td class="left"><?php echo $text_shipping_express; ?></td>
	        </tr>
	      </thead>
	      <tbody>
	        <tr>
	        <td class="left"> 
	        <?php if(isset($express)&&$express){?>
	  			<?php echo $text_express;?> :<a href="<?php echo $express_website;?>"><b><?php echo $express;?></b></a> , <?php echo $text_express_no;?> :<b><?php echo $express_no;?></b>
	  	 	 <?php } ?>
	  		 </td>
	    	 </tr>
	  	</tbody>
	    </table>
    <?php } ?>
   <?php } ?>

    <table class="list">
      <thead>
        <tr>
          <td class="left"><?php echo $column_name; ?></td>
          <td class="left"><?php echo $column_model; ?></td>
          <td class="right"><?php echo $column_quantity; ?></td>
          <td class="right"><?php echo $column_price; ?></td>
          <td class="right"><?php echo $column_total; ?></td>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $product) { ?>
        <tr>
          <td class="left"><?php echo $product['name']; ?>
            <?php foreach ($product['option'] as $option) { ?>
            <br />
            &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
            <?php } ?></td>
          <td class="left"><?php echo $product['model']; ?>
          </td>
          <td class="right"><?php echo $product['quantity']; ?></td>
          <td class="right"><?php echo $product['price']; ?></td>
          <td class="right"><?php echo $product['total']; ?></td>
        </tr>
        <?php } ?>
      </tbody>
      <tfoot>
        <?php foreach ($totals as $total) { ?>
        <tr>
          <td colspan="3"></td>
          <td class="right"><b><?php echo $total['title']; ?>:</b></td>
          <td class="right"><?php echo $total['text']; ?></td>
        </tr>
        <?php } ?>
      </tfoot>
    </table>
    
  <?php if ($comment) { ?>
  <table class="list">
    <thead>
      <tr>
        <td class="left"><?php echo $text_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td class="left"><?php echo $comment; ?></td>
      </tr>
    </tbody>
  </table>
  <?php } ?>
  <?php if ($histories) { ?>
  <h2><?php echo $text_history; ?></h2>
  <table class="list">
    <thead>
      <tr>
        <td class="left"><?php echo $column_date_added; ?></td>
        <td class="left"><?php echo $column_status; ?></td>
        <td class="left"><?php echo $column_comment; ?></td>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($histories as $history) { ?>
      <tr>
        <td class="left"><?php echo $history['date_added']; ?></td>
        <td class="left"><?php echo $history['status']; ?></td>
        <td class="left"><?php echo $history['comment']; ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } ?>
  <?php echo $content_bottom; ?></div>
<?php echo $footer; ?> 