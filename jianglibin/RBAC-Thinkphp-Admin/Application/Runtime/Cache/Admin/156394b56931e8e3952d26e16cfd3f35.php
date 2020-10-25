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
    <i class="Hui-iconfont">&#xe67f;</i> 权限管理 <span class="c-gray en">&gt;</span> 菜单管理<span class="c-gray en">&gt;</span> <?php echo ($actionName); ?>
        <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新"> <i class="Hui-iconfont">&#xe68f;</i></a>
	   </nav>
    <div class="page-container">
        <form action="" method="post" class="form form-horizontal" id="form-member-add" novalidate="novalidate">
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>上级菜单:</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <select class="select" size="1" name="pid">
                        <option value="0">顶级菜单</option>
                        <?php if(is_array($menuList)): foreach($menuList as $key=>$menu): if($menu['menu_id'] == $_GET['menu_pid']): ?><option value="<?php echo ($menu["menu_id"]); ?>" selected><?php echo ($menu["name"]); ?></option>
                                <?php elseif($menu['menu_id'] == $menuInfo['pid']): ?>
                                <option value="<?php echo ($menu["menu_id"]); ?>" selected><?php echo ($menu["name"]); ?></option>
                                <?php else: ?>
                                <option value="<?php echo ($menu["menu_id"]); ?>"><?php echo ($menu["name"]); ?></option><?php endif; endforeach; endif; ?>
                    </select>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>菜单名称:</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <input type="text" class="input-text" value="<?php echo ($menuInfo['name']); ?>" name="name">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>控制器:</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <input type="text" class="input-text" name="controller" value="<?php echo ($menuInfo['controller']); ?>">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>方法:</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <input type="text" class="input-text" name="action" value="<?php echo ($menuInfo['action']); ?>">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>排序权重:</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <input type="text" class="input-text" min="0" max="255" name="step" value="<?php echo ($menuInfo['step']); ?>" placeholder="0-255,越小菜单越靠前">
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>是否启用:</label>
                <div class="col-xs-8 col-sm-3 ">
                    <select class="select" size="1" name="power">
                        <option value="1" <?php if(($menuInfo["power"]) == "1"): ?>selected<?php endif; ?>>启用</option>
                        <option value="0"  <?php if(($menuInfo["power"]) == "0"): ?>selected<?php endif; ?>>关闭</option>
                    </select>
                </div>
            </div>
            <div class="row cl">
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>是否作为菜单显示:</label>
                <div class="col-xs-8 col-sm-3">
                    <select name="status" class="select">
                      <option value="1" <?php if(($menuInfo["status"]) == "1"): ?>selected<?php endif; ?>>显示</option>
                      <option value="0" <?php if(($menuInfo["status"]) == "0"): ?>selected<?php endif; ?>>关闭</option>
                    </select>
                </div>
            </div>
            <div class="row cl">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                    <input type="hidden" name="menu_id" value="<?php echo ($menuInfo["menu_id"]); ?>">
                    <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;提交&nbsp;&nbsp;">
                </div>
            </div>
        </form>
    </div>


</html>