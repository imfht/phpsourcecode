{config_load file=lng.conf section = "strings" scope="global" }<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{$title} @ {$settings.name}</title>
<meta content="yes" name="apple-mobile-web-app-capable" />
<meta content="index,follow" name="robots" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<link href="pics/homescreen.gif" rel="apple-touch-icon" />
<meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
<link href="templates/{$settings.template}/css/style.css" rel="stylesheet" media="screen" type="text/css" />
{if $settings.template!=$settings.template2}<link href="templates/{$settings.template2}/css/style.css" rel="stylesheet" media="screen" type="text/css" />{/if}
<script src="include/js/iphone.js" type="text/javascript"></script>
<script src="include/js/jsval.php" type="text/javascript"></script>
</head>