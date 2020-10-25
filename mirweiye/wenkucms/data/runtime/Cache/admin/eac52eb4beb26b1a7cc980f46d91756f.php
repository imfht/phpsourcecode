<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title><?php echo L('website_manage');?></title>
    <link rel="stylesheet" href="__PUBLIC__/admin/static/css/style.css">
    <link rel="stylesheet" href="__PUBLIC__/admin/layui/css/layui.css">
    
    <link rel="icon" href="__PUBLIC__/admin/static/image/code.png">
</head>
<body>

<!-- layout admin -->
<div class="layui-layout layui-layout-admin">  
    <!-- header -->
    <div class="layui-header my-header">
        <a href="index.html">
            <div class="my-header-logo">七只熊文库系统 </div>
        </a>
        <!-- 顶部左侧添加选项卡监听 -->
        <ul class="layui-nav" lay-filter="side-top-left">
            <?php if(is_array($top_menu)): $i = 0; $__LIST__ = $top_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li class="layui-nav-item"><a href="javascript:;" href-url="<?php echo U($val['module_name'].'/'.$val['action_name'], array('menuid'=>$val['id'])); echo ($val["data"]); ?>" data-id="<?php echo ($val["id"]); ?>"><i class="layui-icon">&#<?php echo ($val["ico"]); ?>;</i><?php echo ($val["name"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>

        <!-- 顶部右侧添加选项卡监听 -->
        <ul class="layui-nav my-header-user-nav" lay-filter="side-top-right">
            <li class="layui-nav-item"><a href="./" target="_blank"><?php echo L('site_home');?></a></li>
            <li class="layui-nav-item"><a href="javascript:;" class="pay" href-url="">支持作者</a></li>
            <li class="layui-nav-item"><a class="name" href="javascript:;"><i class="layui-icon">&#xe612;</i>Admin </a></li>
            <li class="layui-nav-item"><a class="name" href="javascript:;" id="logout"> 退出 </a></li>
        </ul>

    </div>
    <!-- side -->
    <div class="layui-side my-side">
        <div class="layui-side-scroll">
            <!-- 左侧主菜单添加选项卡监听 -->
             <ul class="layui-nav layui-nav-tree" lay-filter="side-main">
	<?php if(is_array($left_menu)): $i = 0; $__LIST__ = $left_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$val): $mod = ($i % 2 );++$i;?><li class="layui-nav-item">  
	        <a href="javascript:;"><i class="layui-icon">&#<?php echo ($val["ico"]); ?>;</i><?php echo ($val["name"]); ?></a>
	        <dl class="layui-nav-child">
	        	<?php if(is_array($val['sub'])): $i = 0; $__LIST__ = $val['sub'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sval): $mod = ($i % 2 );++$i;?><dd><a href="javascript:;" href-url="<?php echo U($sval['module_name'].'/'.$sval['action_name'], array('menuid'=>$sval['id'])); echo ($sval["data"]); ?>" data-id="<?php echo ($sval["id"]); ?>"><i class="layui-icon">&#<?php echo ($sval["ico"]); ?>;</i><?php echo ($sval["name"]); ?></a></dd><?php endforeach; endif; else: echo "" ;endif; ?>
	        </dl>
	    </li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>

        </div>
    </div>
    <!-- body -->
    <div class="layui-body my-body">
        <div class="layui-tab layui-tab-card my-tab" lay-filter="card" lay-allowClose="true">
            <ul class="layui-tab-title">
                <li class="layui-this" lay-id="1"><span><i class="layui-icon">&#xe638;</i>欢迎页</span></li>
            </ul>
            <div class="layui-tab-content">
                <div class="layui-tab-item layui-show">
                    <iframe id="iframe" src="<?php echo U('index/panel');?>" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- pay -->
<div class="my-pay-box none">
    <div><img src="__PUBLIC__/admin/static/image/wx.jpg" alt="微信"></div>
</div>

<!-- 右键菜单 -->
<div class="my-dblclick-box none">
    <table class="layui-tab dblclick-tab">
        <tr class="card-refresh">
            <td><i class="layui-icon">&#x1002;</i>刷新当前标签</td>
        </tr>
        <tr class="card-close">
            <td><i class="layui-icon">&#x1006;</i>关闭当前标签</td>
        </tr>
        <tr class="card-close-all">
            <td><i class="layui-icon">&#x1006;</i>关闭所有标签</td>
        </tr>
    </table>
</div>

<script type="text/javascript" src="__PUBLIC__/admin/layui/layui.js"></script>
<script type="text/javascript" src="__PUBLIC__/admin/static/js/vip_comm.js"></script>
<script type="text/javascript">
layui.use(['layer','jquery','form'], function () {
   // 操作对象
    var layer = layui.layer,$ = layui.jquery,form = layui.form;

        // 提交退出
        $("#logout").click(function() {
            $.post("<?php echo U('index/logout');?>",function(res){
                if(res.status.status ==  1){
                    layer.msg(res.status.info,{time:1800},function(){
                        location.href = res.status.url;
                    });
                }else {
                    layer.msg('操作失败',{time:1800},function(){
                        location.href = "<?php echo U('index/login');?>";
                    });
                }
            });
            return false;
        });
});
</script>
</body>
</html>