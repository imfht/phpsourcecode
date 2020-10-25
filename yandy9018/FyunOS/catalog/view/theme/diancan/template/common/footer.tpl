<div id="footer">
  <div class="column">
    <h3><?php echo $text_information; ?></h3>
    <ul>
      <?php foreach ($informations as $information) { ?>
      <li><a  href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_service; ?></h3>
    <ul>
      <li><a rel="nofollow" href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
      <li><a rel="nofollow" href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
      <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_extra; ?></h3>
    <ul>
      <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
      <li><a rel="nofollow" href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
      <li><a rel="nofollow" href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
      <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
    </ul>
  </div>
  <div class="column">
    <h3><?php echo $text_account; ?></h3>
    <ul>
      <li><a rel="nofollow" href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
      <li><a rel="nofollow" href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
      <li><a rel="nofollow" href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
      <li><a rel="nofollow" href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
    </ul>
  </div>
</div>

<div id="powered"><?php echo $powered; ?> <?php echo $google_analytics; ?></div>

</div>
</div>
</body>
<script type="text/javascript" src="catalog/view/javascript/jquery/go-top.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
(new GoTop()).init({
	pageWidth		:960,
	nodeId			:'go-top',
	nodeWidth		:50,
	distanceToBottom	:125,
	hideRegionHeight	:130,
	text			:'Top'
});
/* ]]> */
</script>
<script type="text/javascript">
$('.box-product .ym-g25:nth-child(4n)').after('<br class="clear" />');
$('.product-grid .product:nth-child(4n)').after('<br class="clear" />');
</script>
</html>

