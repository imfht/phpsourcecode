<!doctype html>
<html>
<head>
{if $this->pageTitle != ""}
<title>{$this->pageTitle}_{$siteinfo->SiteName}</title>
{else}
    <title>{$siteinfo->SiteName}</title>
{/if}
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="keywords" content="{$this->pageKeywords}{$siteinfo->SiteKeywords}" />
<meta name="description" content="{$this->pageDescription}{$siteinfo->SiteIntro}" />
<link rel="stylesheet" type="text/css" href="{Yii::app()->theme->baseUrl}/css/biquge.css"/>
{if $this->action->id != "login" && $this->action->id != "register"}
<script type="text/javascript" src="{Yii::app()->theme->baseUrl}/js/jquery.min.js"></script>
{else}
<script type="text/javascript" src="{Yii::app()->theme->baseUrl}/js/bootstrap.min.js"></script>
{/if}
{*<script type="text/javascript" src="{Yii::app()->theme->baseUrl}/js/m.js"></script>*}
<script type="text/javascript" src="{Yii::app()->theme->baseUrl}/js/bqg.js"></script>
<script>
    fwBaseUrl = '{Yii::app()->baseUrl}/';
    {if !Yii::app()->user->isGuest}
    fwUserId = '{Yii::app()->user->id}';
    fwUserName = '{Yii::app()->user->name}';
    {/if}
</script>
</head>
<body>
	<div id="wrapper">
		<script>login();</script>
		<div class="header">
			<div class="header_logo">
				<a href="{Yii::app()->baseUrl}"><img src="{Yii::app()->theme->baseUrl}/images/logo.png" border="0" /></a>
			</div>
			<script>bqg_panel();</script>
		</div>
		<div class="nav">
			<ul>
				<li><a href="{Yii::app()->baseUrl}/">首页</a></li>
                {*{foreach Category::getMenus() as $menu}*}
                    {*{assign var="url" value=$this->createUrl('category/index', ['title' => $menu.shorttitle])}*}
                    {*<li><a href="{menulink menu=$menu}">{$menu.title}</a></li>*}
                {*{/foreach}*}

                {novel_menu name="top_menu"}
                    <li><a href="{novel_category_link id=$item->id}">{$item->title}</a></li>
                {/novel_menu}
			</ul>
		</div>


	{$content}
        <a name="footer"></a>
		<div id="firendlink">
		友情连接：
            {novel_friend_link name="friend_link"}
                <a href="{$item->linkurl}">{$item->title}</a>
            {/novel_friend_link}
		</div>
		<div class="footer">
			<div class="footer_link"></div>
			<div class="footer_cont">
				<script>footer();</script>
			</div>
		</div>

    </div>
</body>
</html>
<!-- spend time:{$TIME} -->
{*{debug}*}