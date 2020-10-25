<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>后台管理模板</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ asset('iassets/plugins/layui/css/layui.css') }}" media="all" />
    <link rel="stylesheet" href="{{ asset('iassets/css/global.css') }}" media="all">
    <link rel="stylesheet" href="{{ asset('iassets') }}/plugins/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('iassets') }}/css/style.css">
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>

<body>
<div class="layui-layout layui-layout-admin">
    <div class="layui-header header">
        <div class="layui-main">
            <div class="admin-login-box">
                <a class="logo" href="/">
                    <span>后台管理系统</span>
                </a>
            </div>
            <ul class="layui-nav admin-header-item">
                <li class="layui-nav-item">
                    <a href="javascript:;" class="admin-header-user">
                        <span style="padding-right:20px;">{{ auth()->guard('admin')->user()->nickname }}</span>
                    </a>
                    <dl class="layui-nav-child">
                        <dd>
                            <a href="{{ route('admin.edit.account') }}"><i class="fa fa-keyboard-o" aria-hidden="true"></i> 修改密码</a>
                        </dd>
                        <dd>
                            <a href="{{ route('admin.login.history') }}"><i class="fa fa-history" aria-hidden="true"></i> 登录历史</a>
                        </dd>
                        <dd>
                            <a href="javascript:;" data-route="{{ route('admin.logout') }}" id="logout"><i class="fa fa-sign-out" aria-hidden="true"></i> 注销</a>
                        </dd>
                    </dl>
                </li>
            </ul>
        </div>
    </div>
    <div class="layui-side layui-bg-black" id="admin-side">
        <div class="layui-side-scroll" id="admin-navbar-side" lay-filter="side">
            @inject('menu', 'Services\MenuService')
            {!! $menu->getMenus() !!}
        </div>
    </div>
    <div class="layui-body"  id="admin-body">
        <div class="layui-tab admin-nav-card layui-tab-brief" lay-filter="admin-tab">
            <div class="layui-tab-content" style="min-height: 150px; padding: 10px; font-size: 12px;">
                <div class="layui-tab-item layui-show">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <div class="site-tree-mobile layui-hide">
        <i class="layui-icon">&#xe602;</i>
    </div>
    <div class="site-mobile-shade"></div>
    <script type="text/javascript" src="{{ asset('iassets') }}/plugins/layui/layui.js"></script>
    <script src="{{ asset('iassets/js/site.js') }}"></script>
    @yield('script')
    @include('layouts.message')
</div>
</body>

</html>