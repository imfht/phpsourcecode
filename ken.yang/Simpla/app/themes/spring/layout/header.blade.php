<?php
/**
 * 变量：
 * --$siteLogo：站点LOGO
 * --$siteUrl：站点地址
 * --$siteName：站点名字
 * --$menu_top：顶部菜单
 *      --list:菜单列表
 *      --content：菜单内容
 * 
 * --$is_allow_login：是否允许前台登录，1位允许，0位不允许
 * --$is_allow_register：是否允许注册，1为允许，0为不允许
 * 
 * 方法：
 * --Auth::user()：获取登录用户信息
 * --User::find()：获取用户信息
 */
?>
<nav role="navigation" class="navbar">
    <div class="container">
        <div class="navbar-header">
            <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
                <span class="sr-only">切换</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <!--logo-->
            @if($siteLogo)
            <a href="{{$siteUrl}}" class="navbar-brand"><img src="/{{$siteLogo}}" width="87" height="31"/></a>
            @else
            <a href="{{$siteUrl}}" class="navbar-brand">{{$siteName}}</a>
            @endif
        </div>

        <!--菜单-->
        <ul class="nav navbar-nav">
            {{$menu_top['content']}}
        </ul>
        <!--登陆、注册-->
        <ul class="nav navbar-nav navbar-right">
            {{$login_and_register}}
        </ul>
        <!--搜索-->
        <form class="navbar-form navbar-right" role="search" method="get" action="/search">
            <div class="input-group">
                <input type="text" name="key" class="form-control" placeholder="Search" required="" maxlength="10">
                <span class="input-group-btn">
                    <button class="btn btn-default" type="submit">搜索</button>
                </span>
            </div><!-- /input-group -->
        </form>
    </div>
</nav>