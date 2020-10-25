<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="{S_CONTENT_DIRECTION}" lang="en" xml:lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset={S_CONTENT_ENCODING}" />
<meta http-equiv="content-style-type" content="text/css" />
<meta name="copyright" content="2001, 2005 phpBB Group" />
{META}
<title>{SITENAME} &bull; {PAGE_TITLE}</title>
<!-- BEGIN switch_enable_pm_popup -->
<script type="text/javascript">
<!--
	if ( {PRIVATE_MESSAGE_NEW_FLAG} )
	{
		window.open('{U_PRIVATEMSGS_POPUP}', '_phpbbprivmsg', 'height=225,resizable=yes,width=400');;
	}
//-->
</script>
<!-- END switch_enable_pm_popup -->
<script type="text/javascript" src="templates/prosilver/forum_fn.js"></script>
<link href="templates/prosilver/{T_HEAD_STYLESHEET}" rel="stylesheet" type="text/css" media="screen, projection" />
</head>
<body id="phpbb" class="section-index {S_CONTENT_DIRECTION}">
<div id="wrap">
	<a id="top" name="top" accesskey="t"></a>
	<div id="page-header">
		<div class="headerbar">
			<div class="inner"><span class="corners-top"><span></span></span>
			<div id="site-description">
				<a href="{U_INDEX}" title="{L_INDEX}" id="logo"><img src="templates/prosilver/images/site_logo.gif" width="139" height="52" alt="" title="{SITENAME}" /></a>
				<h1>{SITENAME}</h1>
				<p>{SITE_DESCRIPTION}</p>
			</div>
			<div id="search-box">
				<form action="{U_SEARCH}" method="get" id="search">
				<fieldset>
					<input name="search_keywords" id="keywords" type="text" maxlength="128" title="" class="inputbox search" value="{L_SEARCH}" onclick="if(this.value=='{L_SEARCH}')this.value='';" onblur="if(this.value=='')this.value='{L_SEARCH}';" /> 
					<input class="button2" value="{L_SEARCH}" type="submit" />
					{S_HIDDEN_FIELDS}
				</fieldset>
				</form>
			</div>
			<span class="corners-bottom"><span></span></span></div>
		</div>