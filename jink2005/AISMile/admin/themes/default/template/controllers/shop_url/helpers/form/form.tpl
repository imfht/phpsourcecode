{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{extends file="helpers/form/form.tpl"}

{block name=script}
	$(document).ready(function(){
		fillShopUrl();
		checkMainUrlInfo();
		$('#domain, #physical_uri, #virtual_uri').keyup(fillShopUrl);

		var change_domain_value = false;
		$('#domain').keydown(function()
		{
			if (!$('#domain_ssl').val() || $('#domain_ssl').val() == $('#domain').val())
			{
				change_domain_value = true;
			}
		});

		$('#domain_ssl').keydown(function()
		{
			change_domain_value = false;
		});

		$('#domain').keyup(function()
		{
			if (change_domain_value)
			{
				change_domain_value = false;
				$('#domain_ssl').val($('#domain').val());
			}
		});

		$('#virtual_uri').keyup(function()
		{
			txt = $('#virtual_uri').val()
			txt = txt.replace(' ', '-');
			$('#virtual_uri').val(txt);
		});

	});

	var shopUrl = {$js_shop_url};

	function fillShopUrl()
	{
		var domain = $('#domain').val();
		var physical = $('#physical_uri').val();
		var virtual = $('#virtual_uri').val();
		url = ((domain) ? domain : '???');
		if (physical)
		url += '/'+physical;
		if (virtual)
			url += '/'+virtual+'/';
		url = url.replace(/\/+/g, "/");
		$('#final_url').val('http://'+url);
	};

	function checkMainUrlInfo(shopID)
	{
		if (!shopID)
			shopID = $('#id_shop').val();

		if (!shopUrl[shopID])
		{
			$('#main_off').attr('disabled', true);
			$('#main_on').attr('checked', true);
			$('#mainUrlInfo').css('display', 'block');
			$('#mainUrlInfoExplain').css('display', 'none');
		}
		else
		{
			$('#main_off').attr('disabled', false);
			$('#mainUrlInfo').css('display', 'none');
			$('#mainUrlInfoExplain').css('display', 'block');
		}
	}
{/block}
