<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>
        权限管理
    </title>
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui/css/H-ui.min.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/css/H-ui.admin.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/skin/default/skin.css" id="skin" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/static/h-ui.admin/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/lib/Hui-iconfont/1.0.7/iconfont.css" />
    <link rel="stylesheet" type="text/css" href="/ar/Public/lib/icheck/icheck.css" />

    
    <script type="text/javascript" src="/ar/Public/lib/jquery/1.9.1/jquery.min.js"></script>
    <script type="text/javascript" src="/ar/Public/js/jquery.icheck.min.js"></script>

    <script type="text/javascript" src="/ar/Public/lib/layer/2.1/layer.js"></script>
    <script type="text/javascript" src="/ar/Public/static/h-ui/js/H-ui.js"></script>
    <script type="text/javascript" src="/ar/Public/static/h-ui.admin/js/H-ui.admin.js"></script>
    <script type="text/javascript" src="/ar/Public/laydate/laydate.dev.js"></script>
    <script type="text/javascript" src="/ar/Public/lib/layer/2.1/layer.js"></script>
    <script type="text/javascript" src="/ar/Public/lib/laypage/1.2/laypage.js"></script>
    <link rel="stylesheet" type="text/css" href="/ar/Public/ichartjs1.2/samples/css/demo.css" />
    <script type="text/javascript" src="/ar/Public/ichartjs1.2/ichart.1.2.min.js"></script> 
    
</head>


    <nav class="breadcrumb">
    <i class="Hui-iconfont">&#xe67f;</i> 权限管理 <span class="c-gray en">&gt;</span> 菜单管理
        <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新"> <i class="Hui-iconfont">&#xe68f;</i></a>
	</nav>
    <div class="page-container">
        <form class="form-inline definewidth m20" id="searchForm" action="">
        </form>
        <div class="cl pd-5 bg-1 bk-gray mt-20"><span class="l">
        <a class="btn btn-primary radius" data-title="添加菜单" href="<?php echo U('Menu/add');?>"><i class="Hui-iconfont">
            </i>
            添加菜单</a></span></div>
            <br>
        <table class="table table-border table-bordered table-bg table-sort">
            <thead>
            <tr>
                <th>菜单ID</th>
                <th>菜单名称</th>
                <th>控制器</th>
                <th>方法</th>
                <th>是否启用</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            
            <?php if(is_array($menuList)): foreach($menuList as $key=>$vo): ?><tr >
                   <td><?php echo ($vo["menu_id"]); ?></td>
                   <td><?php echo ($vo["name"]); ?></td>
                   <td><?php echo ($vo["controller"]); ?></td>
                   <td><?php echo ($vo["action"]); ?></td>
                   <td>
                   <?php if($vo["power"] == 1): ?>启用
                       <?php else: ?>
                       关闭<?php endif; ?>
                   </td>
                   <td>
                       <a href="<?php echo U('Menu/edit',array('menu_id' => $vo['menu_id']));?>">编辑</a>
                       <a href="<?php echo U('Menu/addChild',array('menu_pid' => $vo['menu_id']));?>">添加子菜单</a>
                       <a href="<?php echo U('Menu/del',array('menu_id' => $vo['menu_id']));?>" onclick="return confirm('您确定要删除此菜单吗?')">删除菜单</a>
                   </td>
                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="pager">
            <?php echo ($page); ?>
        </div>
    </div>



</html>