<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title}</title>
<META content=IE=8 http-equiv=X-UA-Compatible>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link rel="stylesheet" type="text/css" href="__PUBLIC__/css/base.css" />
<link rel="stylesheet" type="text/css" href="__APPURL__/install.css" />
</head>
<body>
<div class="w900 auto">
  <div class="install_title">{$title}</div>
  <div class="install_left">
    <ul>
	  {loop $menu $action $title}
		{if $action == ACTION_NAME}
			<li class="on">{$title}</li>
		{else}
			<li>{$title}</li>	
		{/if}
	  {/loop}
    </ul>
  </div>
  {include file="$__template_file"}
</div>
</body>
</html>