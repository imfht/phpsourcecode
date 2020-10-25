<?php /*a:2:{s:50:"D:\phpstudy_pro\WWW\tp\view\index\index\index.html";i:1601857137;s:52:"D:\phpstudy_pro\WWW\tp\view\index\common\static.html";i:1591060588;}*/ ?>
<!doctype html>
<html class="x-admin-sm">
    <head>
        <head>
    <meta charset="UTF-8">
    <title>BOOL酒店管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,user-scalable=yes, minimum-scale=0.4, initial-scale=0.8,target-densitydpi=low-dpi" />
    <link rel="stylesheet" href="/static/admin/css/font.css">
    <link rel="stylesheet" href="/static/admin/css/xadmin.css">
    <script src="/static/admin/lib/layui/layui.js" charset="utf-8"></script>
    <script type="text/javascript" src="/static/admin/js/xadmin.js"></script>

    <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

    <script src="https://cdn.bootcdn.net/ajax/libs/jquery/2.0.3/jquery.js"></script>
    <link href="/static/toastr/toastr.css" rel="stylesheet"/>
    <script src="/static/toastr/toastr.js"></script>
</head>
        <!-- 让IE8/9支持媒体查询，从而兼容栅格 -->
        <!--[if lt IE 9]>
          <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
          <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
        <script>
            // 是否开启刷新记忆tab功能
            // var is_remember = false;
        </script>
    </head>
    <body class="index">
        <!-- 顶部开始 -->
        <div class="container">
            <div class="logo">
                <a href="/static/admin/index.html">Bool酒店管理系统</a></div>
            <div class="left_open">
                <a><i title="展开左侧栏" class="iconfont">&#xe699;</i></a>
            </div>

            <ul class="layui-nav right" lay-filter="">
                <li class="layui-nav-item">
                    <a href="javascript:;"><?php echo session('admin');; ?></a>
                    <dl class="layui-nav-child">
                        <!-- 二级菜单 -->
                        <dd>
                            <a onclick="xadmin.open('个人信息','http://www.baidu.com')">个人信息</a></dd>
                        <dd>
                            <a onclick="xadmin.open('切换帐号','http://www.baidu.com')">切换帐号</a></dd>
                        <dd>
                            <a href="<?php echo url('index/login/logout'); ?>">退出</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item to-index">
                    <a href="/">前台首页</a></li>
            </ul>
        </div>
        <!-- 顶部结束 -->
        <!-- 中部开始 -->
        <!-- 左侧菜单开始 -->
        <div class="left-nav">
            <div id="side-nav">
                <ul id="nav">

                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="员工管理">&#xe726;</i>
                            <cite>管理员管理</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('员工列表','/index/admins/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>管理员列表</cite></a>
                            </li>
                        </ul>
                    </li>

                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="房间管理">&#xe722;</i>
                            <cite>房间管理</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('楼栋设置','/index/buildings/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>楼栋设置</cite></a>
                            </li>
                            <li>
                                <a onclick="xadmin.add_tab('楼层设置','/index/storeys/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>楼层设置</cite></a>
                            </li>
                            <li>
                                <a onclick="xadmin.add_tab('房型设置','/index/layouts/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>房型设置</cite></a>
                            </li>
                            <li>
                                <a onclick="xadmin.add_tab('房间信息','/index/rooms/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>房间信息</cite></a>
                            </li>
                        </ul>
                    </li>

<!--                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="仓库管理">&#xe726;</i>
                            <cite>仓库管理</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('商品模型','/index/goods/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>商品模型</cite></a>
                            </li>
                            <li>
                                <a onclick="xadmin.add_tab('查看商品','/index/goods/select_goods')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>查看商品</cite></a>
                            </li>
                            <li>
                                <a onclick="xadmin.add_tab('采购商品','/index/purchase/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>采购商品</cite></a>
                            </li>
                            <li>
                                <a onclick="xadmin.add_tab('入库登记','/index/purchase/warehousing')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>入库登记</cite></a>
                            </li>
                        </ul>
                    </li>-->

                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="优惠活动">&#xe702;</i>
                            <cite>优惠活动</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('优惠活动','/index/activity/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>优惠活动</cite></a>
                            </li>
                        </ul>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('房间优惠','/index/Pricesystem/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>房间优惠</cite></a>
                            </li>
                        </ul>
                    </li>

<!--                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="插件市场">&#xe6a9;</i>
                            <cite>插件市场</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('普通用户','/index/Pricesystem/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>普通用户</cite></a>
                            </li>
                        </ul>
                    </li>-->

                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="硬件管理">&#xe6a9;</i>
                            <cite>硬件管理</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a onclick="xadmin.add_tab('门锁设置','/index/Pricesystem/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>门锁设置</cite></a>
                            </li>
                            <li>
                                <a onclick="xadmin.add_tab('读卡器设置','/index/Pricesystem/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>读卡器设置</cite></a>
                            </li>
                        </ul>
                    </li>


                    <li>
                        <a href="javascript:;">
                            <i class="iconfont left-nav-li" lay-tips="系统设置">&#xe6ae;</i>
                            <cite>系统设置</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                        <ul class="sub-menu">
                            <li>
                                <a href="javascript:;">
                                    <i class="iconfont">&#xe70b;</i>
                                    <cite>基础设置</cite>
                                    <i class="iconfont nav_right">&#xe697;</i></a>
                                <ul class="sub-menu">
                                    <li>
                                        <a onclick="xadmin.add_tab('支付方式','/index/systems/index')">
                                            <i class="iconfont">&#xe6a7;</i>
                                            <cite>支付方式</cite></a>
                                    </li>
                                    <li>
                                        <a onclick="xadmin.add_tab('宾客来源','/index/systems/guest')">
                                            <i class="iconfont">&#xe6a7;</i>
                                            <cite>宾客来源</cite></a>
                                    </li>
                                    <li>
                                        <a onclick="xadmin.add_tab('证件类型','/index/systems/identity')">
                                            <i class="iconfont">&#xe6a7;</i>
                                            <cite>证件类型</cite></a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a href="javascript:;">
                                    <i class="iconfont">&#xe70b;</i>
                                    <cite>计费方式</cite>
                                    <i class="iconfont nav_right">&#xe697;</i></a>
                                <ul class="sub-menu">
                                    <li>
                                        <a onclick="xadmin.add_tab('普通用户','/index/charges/index')">
                                            <i class="iconfont">&#xe6a7;</i>
                                            <cite>普通用户</cite></a>
                                    </li>
                                    <li>
                                        <a onclick="xadmin.add_tab('酒店会员','/index/charges/vip')">
                                            <i class="iconfont">&#xe6a7;</i>
                                            <cite>酒店会员</cite></a>
                                    </li>
                                </ul>
                            </li>

                            <li>
                                <a onclick="xadmin.add_tab('班次设置','/index/classe/index')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>班次设置</cite></a>
                            </li>

                            <li>
                                <a onclick="xadmin.add_tab('短信设置','unicode.html')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>夜审设置</cite></a>
                            </li>

                            <li>
                                <a onclick="xadmin.add_tab('短信设置','unicode.html')">
                                    <i class="iconfont">&#xe6a7;</i>
                                    <cite>短信设置</cite></a>
                            </li>

                            <?php if(file_exists($file)): ?>
                                <li>
                                    <a onclick="xadmin.add_tab('语音设置','/apply/voice/index')">
                                        <i class="iconfont">&#xe6a7;</i>
                                        <cite>语音设置</cite></a>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </li>


<!--                    <li>
                        <a onclick="xadmin.add_tab('更新系统','/index/updates/index')">
                            <i class="iconfont left-nav-li" lay-tips="更新系统">&#xe6a9;</i>
                            <cite>更新系统</cite>
                            <i class="iconfont nav_right">&#xe697;</i></a>
                    </li>-->

                </ul>
            </div>
        </div>
        <!-- <div class="x-slide_left"></div> -->
        <!-- 左侧菜单结束 -->
        <!-- 右侧主体开始 -->
        <div class="page-content">
            <div class="layui-tab tab" lay-filter="xbs_tab" lay-allowclose="false">
                <ul class="layui-tab-title">
                    <li class="home">
                        <i class="layui-icon">&#xe68e;</i>我的桌面</li></ul>
                <div class="layui-unselect layui-form-select layui-form-selected" id="tab_right">
                    <dl>
                        <dd data-type="this">关闭当前</dd>
                        <dd data-type="other">关闭其它</dd>
                        <dd data-type="all">关闭全部</dd></dl>
                </div>
                <div class="layui-tab-content">
                    <div class="layui-tab-item layui-show">
                        <iframe src="<?php echo url('index/index/welcome'); ?>" frameborder="0" scrolling="yes" class="x-iframe"></iframe>
                    </div>
                </div>
                <div id="tab_show"></div>
            </div>
        </div>
        <div class="page-content-bg"></div>
        <style id="theme_style"></style>
        <!-- 右侧主体结束 -->
        <!-- 中部结束 -->
    </body>

</html>

<script src="https://cdn.bootcdn.net/ajax/libs/layer/1.8.5/extend/layer.ext.js"></script>
<script>
    // 假设服务端ip为127.0.0.1
    ws = new WebSocket("ws://127.0.0.1:8282");
    ws.onopen = function() {

        //右下弹出
        layer.open({
            type: 1
            ,offset: 'rb'
            ,content: '<div style="padding: 20px 80px;">欢迎使用bool酒店管理系统</div>'
            ,btn: '关闭全部'
            ,btnAlign: 'c' //按钮居中
            ,shade: 0 //不显示遮罩
            ,anim: 2
            ,yes: function(){
                layer.closeAll();
            }
        });

        var audio= new Audio("/static/voice/2/welcome.mp3");

        audio.play();//播放
        ws.send('tom');

    };
    ws.onmessage = function(e) {
        // alert("收到服务端的消息：" + e.data);
        console.log("收到服务端的消息：" + e.data);
    };
</script>