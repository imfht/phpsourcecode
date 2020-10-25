{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="{$lang_iso}" lang="{$lang_iso}">
	<head>
		<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>	
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		
		{if isset($meta_description)}
		<meta name="description" content="{$meta_description|escape:'htmlall':'UTF-8'}" />
		{/if}
		
		{if isset($meta_keywords)}
		<meta name="keywords" content="{$meta_keywords|escape:'htmlall':'UTF-8'}" />
		{/if}
		
		<meta name="robots" content="{if isset($nobots)}no{/if}index,follow" />
		<link rel="shortcut icon" href="{$favicon_url}" />
		<link href="{$css_mobile_dir}maintenance.css" rel="stylesheet" type="text/css" />
	</head>
	<body>
	<!-- Maintenance container -->
	<div data-role="content" id="maintenance" class="maintenance">
	<p id="store"><img src="{$content_dir}img/logo.jpg" alt="logo" /><br /></p>
	<p id="message">
				{l s='In order to perform site maintenance, our online shop has shut down temporarily.'}<br /><br />
				{l s='We apologize for the inconvenience and ask that you please try again later.'}
	</p>
	<p id="milebiz"> &nbsp; </p>
	</div>
	<!-- END Maintenance container -->
	</body>
</html>