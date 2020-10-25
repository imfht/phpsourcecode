{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<script type="text/javascript">
$('document').ready(function(){
	$('#mailalerts_block_extra_add').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'add'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_add').slideUp(function() {
			    		$('#mailalerts_block_extra_added').slideDown("slow");
			    	});
			    	
				}
		 	}
		});
	});
	$('#mailalerts_block_extra_remove').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'remove'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_remove').slideUp(function() {
			    		$('#mailalerts_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#mailalerts_block_extra_added').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'remove'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_added').slideUp(function() {
			    		$('#mailalerts_block_extra_removed').slideDown("slow");
			    	});

				}
		 	}
		});
	});
	$('#mailalerts_block_extra_removed').click(function(){
		$.ajax({
			url: "{$link->getModuleLink('mailalerts', 'actions', ['process' => 'add'])}",
			type: "POST",
			data: {
				"id_product": {$smarty.get.id_product}
			},
			success: function(result){
				if (result == '0')
				{
			    	$('#mailalerts_block_extra_removed').slideUp(function() {
			    		$('#mailalerts_block_extra_added').slideDown("slow");
			    	});

				}
		 	}
		});
	});
})
</script>

{if !$isCustomerMailAlert AND $isLogged}
<li id="mailalerts_block_extra_add" class="add">
	{l s='Add this product to my favorites' mod='mailalerts'}
</li>
{/if}
{if $isCustomerMailAlert AND $isLogged}
<li id="mailalerts_block_extra_remove">
	{l s='Remove this product from my favorites' mod='mailalerts'}
</li>
{/if}

<li id="mailalerts_block_extra_added">
	{l s='Remove this product from my favorites' mod='mailalerts'}
</li>
<li id="mailalerts_block_extra_removed">
	{l s='Add this product to my favorites' mod='mailalerts'}
</li>