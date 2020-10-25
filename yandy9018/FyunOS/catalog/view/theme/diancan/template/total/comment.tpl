<div id="comment">
  <div class="cart-heading"><?php echo $heading_title; ?></div>
  <div class="cart-content" id="comment">
  <textarea name="comment" rows="8" style="width: 98%;" id="order_comment"><?php echo $comment; ?></textarea>
  <br /><br />
  <a id="button-comment" class="button"><span><?php echo $button_comment; ?></span></a></div>
</div>
<script type="text/javascript"><!--
$('#button-comment').bind('click', function() {
	if($('#comment #order_comment').attr('value')){
		$.ajax({
			type: 'POST',
			url: 'index.php?route=total/comment/add',
			data: $('#comment :input'),
			dataType: 'json',		
			beforeSend: function() {
				$('.success, .warning').remove();
				$('#button-comment').attr('disabled', true);
				$('#button-comment').after('<span class="wait">&nbsp;<img src="catalog/view/theme/default/image/loading.gif" alt="" /></span>');
			},
			complete: function() {
				$('#button-comment').attr('disabled', false);
				$('.wait').remove();
			},		
			success: function(json) {
				if(json['success']){
					$('#comment').prepend('<div class="success" style="display: none;">' + json['success'] + '</div>');
					$('.success').fadeIn('slow');

					$('#comment .cart-heading').click();
				}
					
			}
		});
	}
});
//--></script> 