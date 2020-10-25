<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
    <meta charset="utf-8">
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>
        后台人员管理
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
    <i class="Hui-iconfont">&#xe67f;</i> 后台人员管理 <span class="c-gray en">&gt;</span> 角色管理
        <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新"> <i class="Hui-iconfont">&#xe68f;</i></a>
	</nav>
    <div class="page-container">
        <form class="form-inline definewidth m20" id="searchForm" action="">
        </form>
        <div class="cl pd-5 bg-1 bk-gray mt-20"><span class="l">
        <a class="btn btn-primary radius" data-title="添加菜单" href="<?php echo U('Admin/add');?>"><i class="Hui-iconfont">
            </i>
            添加后台用户</a></span></div>
            <br>
        <table class="table table-border table-bordered table-bg table-sort">
            <thead>
            <tr>
                <th>ID</th>
                <th>账号</th>
                <th>所属角色</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($adminList)): foreach($adminList as $key=>$vo): ?><tr >
                   <td><?php echo ($vo["admin_id"]); ?></td>
                   <td><?php echo ($vo["uname"]); ?> </td>
                   <td><?php echo D('Role')->getRole($vo['role_id'],'rname');?></td>
                   <td>
                       <!-- <a href="<?php echo U('Role/privilegeEdit',array('admin_id' => $vo['admin_id']));?>">配置权限</a> -->
                       <a href="<?php echo U('Admin/edit',array('admin_id' => $vo['admin_id']));?>">编辑</a>
                       <a href="<?php echo U('Admin/del',array('admin_id' => $vo['admin_id']));?>" onclick="return confirm('您确定要删除此用户吗?')">删除角色</a>
                   </td>
                </tr><?php endforeach; endif; ?>
            </tbody>
        </table>
        <div class="pager">
            <?php echo ($page); ?>
        </div>
    </div>



</html>