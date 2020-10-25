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
                <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>权限列表:</label>
                <div class="formControls col-xs-8 col-sm-3">
                    <ul>
                        <?php if(is_array($menuList)): foreach($menuList as $key=>$vo): ?><li style="cursor:pointer" class="checkeds" pid="<?php echo ($vo["pid"]); ?>" menuid="<?php echo ($vo["menu_id"]); ?>"><input type="checkbox" name="menuIds[]" value="<?php echo ($vo["menu_id"]); ?>"><?php echo ($vo["name"]); ?></li><?php endforeach; endif; ?>
                    </ul>
                </div>
            </div>
            <div class="row cl">
                <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                    <input type="hidden" name="role_id" value="<?php echo ($_GET['role_id']); ?>">
                    <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;保存&nbsp;&nbsp;">
                </div>
            </div>
        </form>
    </div>


</html>

    <script>

        initCheckeds([<?php echo (implode(',',$checkedMenuIds)); ?>]);

        function initCheckeds(menuIds)
        {
            if ( !menuIds.length )
            {
                return;
            }

            $('.checkeds').each(function(){
                if ( inArray($(this).attr('menuid'), menuIds) )
                {
                    $(this).find('input[type=checkbox]').prop('checked',true);
                }
            });
        }

        function inArray(dst, arr)
        {

            var i = 0,
                len = arr.length;
            for ( ;i <= len; i++ ) 
            {
                if ( dst == arr[i] )
                {
                    return true;
                }
            }

            return false;
        }

        $('.checkeds').click(function(){
            var self = $(this),
                pid = self.attr('pid'),
                menu_id = self.attr('menuid'),
                isChecked = !self.find('input[type=checkbox]').prop('checked'),
                domList = self.parent().find('.checkeds');

            self.find('input[type=checkbox]').prop('checked', isChecked);

            if ( pid == 0 ) 
            {
                checkedChildTree(domList,isChecked,menu_id);
            }
            else
            {
                if ( isChecked ) 
                {
                    checkedParentTree(domList,isChecked,pid);
                }
                if ( !isChecked )
                {
                    checkedChildTree(domList,isChecked,menu_id);
                }
            }
        });

       /**
         * 向上递归选中
         * @author jlb
         */
        function checkedParentTree(domList, isChecked, pid)
        {

            domList.each(function(){
                if ( $(this).attr('menuid') == pid )
                {
                    $(this).find('input[type=checkbox]').prop('checked', isChecked);

                    checkedParentTree(domList, isChecked, $(this).find('input[type=checkbox]').parent().attr('pid'));
                }
            });
        }

        /**
         * 向下递归选中
         * @author jlb
         */
        function checkedChildTree(domList, isChecked, pid)
        {
            domList.each(function(){
                if ( $(this).attr('pid') == pid )
                {
                    $(this).find('input[type=checkbox]').prop('checked', isChecked);
                    checkedChildTree(domList, isChecked, $(this).find('input[type=checkbox]').parent().attr('menuid'));
                }
            });
        }
    </script>