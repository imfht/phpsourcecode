/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

$('document').ready(function(){
	reloadProductComparison();
});

reloadProductComparison = function() {
	$('a.cmp_remove').click(function(){

		var idProduct = $(this).attr('rel').replace('ajax_id_product_', '');

		$.ajax({
  			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
 			async: false,
  			success: function(){
	return true;
}
		});	
	});

	$('input:checkbox.comparator').click(function(){
	
		var idProduct = $(this).attr('value').replace('comparator_item_', '');
		var checkbox = $(this);
		
		if(checkbox.is(':checked'))
{
			$.ajax({
	  			url: 'index.php?controller=products-comparison&ajax=1&action=add&id_product=' + idProduct,
	 			async: true,
	  			success: function(data){
	  				if (data == '0')
	  				{
	  					checkbox.attr('checked', false);
		alert(max_item);
}
	  			},
	    		error: function(){
	    			checkbox.attr('checked', false);
	    		}
			});	
		}
		else
		{
			$.ajax({
	  			url: 'index.php?controller=products-comparison&ajax=1&action=remove&id_product=' + idProduct,
	 			async: true,
	  			success: function(data){
	  				if (data == '0')
	  					checkbox.attr('checked', true);
	    		},
	    		error: function(){
	    			checkbox.attr('checked', true);
	    		}
			});	
		}
	});
}