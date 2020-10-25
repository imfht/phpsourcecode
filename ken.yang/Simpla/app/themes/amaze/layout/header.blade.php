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

<header class="am-topbar am-topbar-fixed-top">
    <div class="am-container">
        <h1 class="am-topbar-brand">
            <a href="/">{{$siteName}}</a>
        </h1>

        <button class="am-topbar-btn am-topbar-toggle am-btn am-btn-sm am-btn-secondary am-show-sm-only"
                data-am-collapse="{target: '#collapse-head'}"><span class="am-sr-only">导航切换</span> <span
                class="am-icon-bars"></span></button>

        <div class="am-collapse am-topbar-collapse" id="collapse-head">
            <!--左边-->
            <ul class="am-nav am-nav-pills am-topbar-nav">
                @foreach($menu_top['list'] as $menu)
                @if(!isset($menu['child']))
                <li><a href="{{$menu['url']}}">{{$menu['title']}}</a></li>
                @else
                <li class="am-dropdown" data-am-dropdown>
                    <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
                        {{$menu['title']}} <span class="am-icon-caret-down"></span>
                    </a>
                    <ul class="am-dropdown-content">
                        @foreach($menu['child'] as $item)
                        <li><a href="{{$item['url']}}">{{$item['title']}}</a></li>
                        @endforeach
                    </ul>
                </li>
                @endif
                @endforeach
            </ul>

            <!--右边-->
            @if ($logged_in)
            <div class="am-topbar-right">
                <ul class="am-nav am-nav-pills am-topbar-nav">
                    <li class="am-dropdown" data-am-dropdown>
                        <a class="am-dropdown-toggle" data-am-dropdown-toggle href="javascript:;">
                            欢迎,{{$users->username}} <span class="am-icon-caret-down"></span>
                        </a>
                        <ul class="am-dropdown-content">
                            <li><a href="/user/{{$users->id}}">个人中心</a></li>
                            @if ($users->roles['rid'] == '3')
                            <li><a href="/admin">后台控制面板</a></li>
                            @endif
                            <li><a href="/logout">退出登录</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
            @else
            @if($is_allow_login)
            <div class="am-topbar-right">
                <a class="am-btn am-btn-primary am-topbar-btn am-btn-sm" href="/register?back_url={{Request::url()}}"><span class="am-icon-user"></span> 注册</a>
            </div>
            @endif
            @if($is_allow_register)
            <div class="am-topbar-right">
                <a class="am-btn am-btn-secondary am-topbar-btn am-btn-sm" href="/login?back_url={{Request::url()}}"><span class="am-icon-pencil"></span> 登录</a>
            </div>
            @endif
            @endif
        </div>
    </div>
</header>