{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/options/options.tpl"}

{block name="after"}
<script type="text/javascript">
function ajaxStoreStates(id_state_selected)
{
    $.ajax({
	url: "ajax.php",
	cache: false,
	data: "ajaxStates=1&id_country="+$('#PS_SHOP_COUNTRY_ID').val() + "&id_state=" + $('#PS_SHOP_STATE_ID').val(),
	success: function(html)
	{
	    if (html == 'false')
	    {
		$("#conf_id_PS_SHOP_STATE_ID").fadeOut();
		$('#id_state option[value=0]').attr("selected", "selected");
	    }
	    else
	    {
		$("#PS_SHOP_STATE_ID").html(html);
		$("#conf_id_PS_SHOP_STATE_ID").fadeIn();
		$('#PS_SHOP_STATE_ID option[value=' + id_state_selected + ']').attr("selected", "selected");
	    }
	}
    });
	$("#conf_id_PS_SHOP_CITY_ID").fadeOut();
}

function ajaxStoreCities(id_city_selected)
{
    $.ajax({
	url: "ajax.php",
	cache: false,
	data: "ajaxCities=1&id_state="+$('#PS_SHOP_STATE_ID').val() + "&id_city=" + $('#PS_SHOP_CITY_ID').val(),
	success: function(html)
	{
	    if (html == 'false')
	    {
		$("#PS_SHOP_CITY_ID").fadeOut();
		$('#id_city option[value=0]').attr("selected", "selected");
	    }
	    else
	    {
		$("#PS_SHOP_CITY_ID").html(html);
		$("#conf_id_PS_SHOP_CITY_ID").fadeIn();
		$('#PS_SHOP_CITY_ID option[value=' + id_city_selected + ']').attr("selected", "selected");
	    }
	}
    });
}

$(document).ready(function(){
    {if isset($categoryData.fields.PS_SHOP_STATE_ID.value)}
    if ($('#PS_SHOP_COUNTRY_ID') && $('#PS_SHOP_STATE_ID'))
    {
	ajaxStoreStates({$categoryData.fields.PS_SHOP_STATE_ID.value});
	{if isset($categoryData.fields.PS_SHOP_CITY_ID.value) && $categoryData.fields.PS_SHOP_CITY_ID.value != ''}
	ajaxStoreCities({$categoryData.fields.PS_SHOP_CITY_ID.value});
	{/if}
	$('#PS_SHOP_COUNTRY_ID').change(function() {
	    ajaxStoreStates();
	});
	$('#PS_SHOP_STATE_ID').change(function() {
	    ajaxStoreCities();
	});
    }
    {/if}
});
</script>
{/block}
