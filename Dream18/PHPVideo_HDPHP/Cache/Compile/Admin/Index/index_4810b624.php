<?php if(!defined('HDPHP_PATH'))exit;C('SHOW_NOTICE',FALSE);?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="renderer" content="webkit">
    <title>后台管理 - <?php echo $hd['config']['SYSTEM_WEBNAME'];?></title>
    <base target="myFrameName">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/jquery-1.11.0.js"></script>
<link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Static/Pintuer/pintuer.css" />
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/pintuer.js"></script>
<script type="text/javascript" src="http://localhost/PHPUnion/Static/Pintuer/respond.js"></script>
    <link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Home/Admin/Theme/Public/css/7eplayer.css" />
    <link rel="stylesheet" type="text/css" href="http://localhost/PHPUnion/Home/Admin/Theme/Public/css/index.css">
    <script type="text/javascript" src="http://localhost/PHPUnion/Home/Admin/Theme/Public/js/menu.js"></script>
</head>
<style type="text/css">
#mainCen{ position:absolute; left:210px; top:0; bottom:0; width:8px; background:url(http://localhost/PHPUnion/Home/Admin/Theme/Public/images/main_cen_bg.png) repeat-y; overflow:auto; padding-top:65px; z-index:3;}
</style>
<script type="text/javascript">
// 左侧菜单 - 左右收缩
$(function(){
  $(window).load(function(){
        $("#leftMenu").mCustomScrollbar();
        $("#mainRight").mCustomScrollbar();
  });
  $("#menuTag").click(function(){
    if ($("#mainLeft").css("left") == "0px"){
      $("#menuTag").attr("src","http://localhost/PHPUnion/Home/Admin/Theme/Public/images/main_cen_jt1.png");
      $("#mainLeft").animate({left:'-210px'});
      $("#mainCen").animate({left:'0px'});
      $("#mainRight").animate({left:'0px'});
    }else{
      $("#menuTag").attr("src","http://localhost/PHPUnion/Home/Admin/Theme/Public/images/main_cen_jt.png");
      $("#mainLeft").animate({left:'0px'});
      $("#mainCen").animate({left:'210px'});
      $("#mainRight").animate({left:'210px'});
    }
  });
})(jQuery);
</script>
<body>
    <div class="container-layout">
        <div id="mainLeft" class="bg-blue">
            <div id="leftHead" class="border-blue-bg text-center">
                <div class="margin-big-top margin-bottom"><img class="radius-circle" src="http://localhost/PHPUnion/Home/Admin/Theme/Public/images/head.jpg" width="100" height="100"></div>
                <div class="height text-white">
                    <span><?php echo $_SESSION['username'];?></span>
                    <div class="clearfix"></div>
                </div>
                <div class="text-small margin-small-top">
                    <a href="javascript:hrefUrl('pass.asp');" class="text-white">修改资料</a>&nbsp;&nbsp;
                    <a href="javascript:window.location.href='<?php echo U('Login/out');?>';" class="text-white">退出系统</a>
                </div>
                <div class="text-small margin-small-top">
                    <a href="<?php echo U('Index/Index/index');?>" class="text-white" target="_blank">前台首页</a>&nbsp;&nbsp;
                    <a href="<?php echo U('Admin/Index/index');?>" class="text-white" target="_top">后台首页</a>
                </div>
            </div>
            <div id="leftMenu" data-mcs-theme="minimal">
                <ul id="accordion" class="accordion text-small" style="padding-left:0px;">
                    <li class="open">
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-asterisk"></span>系统配置
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu menu" style="display:block;padding-left:0px;">
                            <li><a href="<?php echo U('Config/webconfig');?>" style="outline:none;">网站设置</a></li>
                            <li><a href="<?php echo U('ConfigGroup/index');?>" style="outline:none;">配 置 组</a></li>
                            <li><a href="<?php echo U('Route/index');?>" style="outline:none;">路由管理</a></li>
                            <li><a href="<?php echo U('File/index');?>" style="outline:none;">附件管理</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-th-list"></span>频道栏目
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu" style="padding-left:0px;">
                            <li><a href="<?php echo U('Cate/index');?>" style="outline:none;">频道管理</a></li>
                            <li><a href="<?php echo U('Cate/add');?>" style="outline:none;">添加频道</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-play-circle-o"></span>视频管理
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu" style="padding-left:0px;">
                            <li><a href="<?php echo U('Content/index');?>" style="outline:none;">视频列表</a></li>
                            <li><a href="<?php echo U('Flag/index');?>" style="outline:none;">推荐位</a></li>
                            <li><a href="<?php echo U('Tag/index');?>" style="outline:none;">标签云</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-user"></span>用户管理
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu" style="padding-left:0px;">
                            <li><a href="<?php echo U('User/index');?>" style="outline:none;">用户管理</a></li>
                            <li><a href="<?php echo U('Group/index');?>" style="outline:none;">用户等级</a></li>
                            <li><a href="<?php echo U('User/add');?>" style="outline:none;">添加用户</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-user"></span>权限管理
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu" style="padding-left:0px;">
                            <li><a href="<?php echo U('Admin/index');?>" style="outline:none;">管 理 员</a></li>
                            <li><a href="<?php echo U('Role/index');?>" style="outline:none;">等级管理</a></li>
                            <li><a href="<?php echo U('Node/index');?>" style="outline:none;">权限菜单</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-file"></span>模板管理
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu" style="padding-left:0px;">
                            <li><a href="<?php echo U('Theme/index');?>" style="outline:none;">模板主题</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-database"></span>扩展管理
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu" style="padding-left:0px;">
                            <li><a href="<?php echo U('Addons/index');?>" style="outline:none;">插件管理</a></li>
                            <li><a href="<?php echo U('Hooks/index');?>" style="outline:none;">钩子管理</a></li>
                        </ul>
                    </li>
                    <li>
                        <div class="link text-default">
                            <span class="float-left ico-w-25 icon-refresh"></span>缓存管理
                            <span class="float-right icon-angle-down"></span>
                        </div>
                        <ul class="submenu" style="padding-left:0px;">
                            <li><a href="<?php echo U('Cache/index');?>" style="outline:none;">全站缓存</a></li>
                            <li><a href="#" style="outline:none;">生成首页</a></li>
                            <li><a href="#" style="outline:none;">生成频道页</a></li>
                            <li><a href="#" style="outline:none;">生成播放页</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <div id="mainCen"><img id="menuTag" src="http://localhost/PHPUnion/Home/Admin/Theme/Public/images/main_cen_jt.png" width="7" height="23"></div>
        <div id="mainRight" class="margin-big-top padding-left" data-mcs-theme="minimal">
            <!-- Main -->
            <iframe id="myFrameId" name="myFrameName" src="<?php echo U('welcome');?>" scrolling="auto" frameborder="0" width="99%" height="99%"></iframe>
            <!-- Main End -->
        </div>
    </div>
</body>
</html>