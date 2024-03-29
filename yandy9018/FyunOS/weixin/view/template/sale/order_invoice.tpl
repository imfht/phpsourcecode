<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
<head>
<title><?php echo $title; ?></title>
<base href="<?php echo $base; ?>" />
<link rel="stylesheet" type="text/css" href="view/stylesheet/invoice.css" />
</head>
<body>
<?php foreach ($orders as $order) { ?>

<div style="width:649px;height:978px;page-break-after: always;margin:0 auto;border:1px #ddd solid;padding:5px;margin-bottom:5px;">
  <h1><?php echo $order['store_name']; ?> <?php echo $text_invoice; ?> - #<?php echo $order['order_id']; ?></h1>
  <table class="store">
    <tr>
      <td><?php echo $order['store_name']; ?><br />
        <?php echo $order['store_address']; ?><br />
        <?php echo $text_telephone; ?> <?php echo $order['store_telephone']; ?><br />
        <?php if ($order['store_fax']) { ?>
        <?php echo $text_fax; ?> <?php echo $order['store_fax']; ?><br />
        <?php } ?>
       </td>
      <td align="right" valign="top"><table>
      	 <tr>
            <td><b><?php echo $text_order_id; ?></b></td>
            <td>#<?php echo $order['order_id']; ?></td>
          </tr>
          <tr>
            <td><b><?php echo $text_date_added; ?></b></td>
            <td><?php echo $order['date_added']; ?></td>
          </tr>
          <?php if ($order['invoice_no']) { ?>
          <tr>
            <td><b><?php echo $text_invoice_no; ?></b></td>
            <td><?php echo $order['invoice_no']; ?></td>
          </tr>
          <?php if ($order['invoice_date']) { ?>
          <tr>
            <td><b><?php echo $text_invoice_date; ?></b></td>
            <td><?php echo $order['invoice_date']; ?></td>
          </tr>
          <?php } ?>
          <?php } ?>
        </table>
       </td>
    </tr>
  </table>
   <table class="address">
    <tr class="heading">
      <td><b><?php echo $text_ship_to; ?></b></td>
    </tr>
    <tr>
        <td><?php echo $order['shipping_address']; ?></td>
    </tr>
   </table>
   <table class="address">
    <tr class="heading">
      <td colspan="2"><b><?php echo $text_shipping_payment; ?></b></td>
    </tr>
    <tr>
        <td><b><?php echo $text_payment_method; ?></b> 货到付款</td>
  		<td><b><?php echo $text_shipping_method; ?></b> 人工配送</td>
    </tr>
    <tr>
        <td><b><?php echo $text_express; ?>:</b> <?php echo $order['express']; ?></td>
  		<td><b><?php echo $text_express_no; ?>:</b> <?php echo $order['express_website']; ?></td>
    </tr>
   </table>
   
   <table class="product">
    <tr class="heading">
      <td><b><?php echo $column_product; ?></b></td>
      <td><b><?php echo $column_model; ?></b></td>
      <td align="right"><b><?php echo $column_quantity; ?></b></td>
      <td align="right"><b><?php echo $column_price; ?></b></td>
      <td align="right"><b><?php echo $column_total; ?></b></td>
    </tr>
    <?php foreach ($order['product'] as $product) { ?>
    <tr>
      <td><?php echo $product['name']; ?>
        <?php foreach ($product['option'] as $option) { ?>
        <br />
        &nbsp;<small> - <?php echo $option['name']; ?>: <?php echo $option['value']; ?></small>
        <?php } ?></td>
      <td><?php echo $product['model']; ?></td>
      <td align="right"><?php echo $product['quantity']; ?></td>
      <td align="right"><?php echo $product['price']; ?></td>
      <td align="right"><?php echo $product['total']; ?></td>
    </tr>
    <?php } ?>
    <?php foreach ($order['total'] as $total) { ?>
    <tr>
      <td align="right" colspan="4"><b><?php echo $total['title']; ?>:</b></td>
      <td align="right"><?php echo $total['text']; ?></td>
    </tr>
    <?php } ?>
  </table>
  <?php if ($order['comment']) { ?>
  <table class="comment">
    <tr class="heading">
      <td><b><?php echo $column_comment; ?></b></td>
    </tr>
    <tr>
      <td><?php echo $order['comment']; ?></td>
    </tr>
  </table>
  <?php } ?>

</div>

<?php } ?>
</body>
</html>