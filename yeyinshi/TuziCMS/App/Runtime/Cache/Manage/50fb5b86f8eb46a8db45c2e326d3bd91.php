<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo ($sitename); ?> - <?php echo (C("setting.Copyright")); ?> <?php echo (C("setting.Version")); ?> <?php echo (C("setting.Code")); ?></title>
<script language="javascript" type="text/javascript" src="/tuzicms/App/Manage/View/Default/js/jquery.js"></script>
<script src="/tuzicms/App/Manage/View/Default/js/frame.js" language="javascript" type="text/javascript"></script>
<link href="/tuzicms/App/Manage/View/Default/css/style.css" rel="stylesheet" type="text/css" />

<!--[if IE 6]>
<script src="/tuzicms/App/Manage/View/Default/Js/DD_belatedPNG.js" language="javascript" type="text/javascript"></script>
<script>
  DD_belatedPNG.fix('.nav ul li a,.top_link ul li,background');   /* string argument can be any CSS selector */
</script>
<![endif]-->
</head>
<body class="showmenu">

<script src="/tuzicms/App/Manage/View/Default/js/common.js" type="text/javascript"></script>
 <script type="text/javascript">
  function clear_del()
  {
   if(confirm("确定要删除数据吗？"))
   {
	subform("<?php echo U('Category/delall');?>");
   }
  }
 </script>

<table width="100%" height="31px" border="0" cellpadding="0" cellspacing="0" class="left_topbg" id="table2">
      <tr>
        <td height="31"><div class="titlebt">栏目管理</div></td>
      </tr>
    </table>

<div class="main">
        
    <div class="operate">
        <div class="left">
		<input type="button" onclick="window.open('/tuzicms/index.php/manage/category/add','main')" target="main" class="btn_blue" value="添加栏目">
		
		<input class="btn_blue" type="button" onClick="return clear_del();"  value="删除" />   
		
		<input type="button" onclick="javascript:document.form_do.submit();"  class="btn_blue" value="更新排序">
       

		</div>
    </div>
    <div class="list">    
    <form action="/tuzicms/index.php/manage/category/sortcate" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input  name="chkall" type="checkbox" id="chkall" onclick="selectall(this.form)"></th>
                <th>编号</th>
                <th>名称</th>
                <th>栏目模型</th>
                <th>排序</th>
				<th>导航栏显示</th>
                <th>操作</th>
            </tr>
			
			<?php if(empty($vlist)): ?><tr>
			<td colspan="7">本区域暂无数据显示...</td>
			</tr>
			<?php else: ?> 
			<?php if(is_array($vlist)): $i = 0; $__LIST__ = $vlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                <td><input type="checkbox" name="id[]" value="<?php echo ($v["id"]); ?>" ></td>
                <td><?php echo ($v["id"]); ?></td>
				
                <td class="aleft">
				<?php if($v['column_link']==1): echo ($v["html"]); ?> <span style="color:#000000"><?php echo ($v["column_name"]); ?></span>
				<?php else: ?>
				<?php echo ($v["html"]); ?> <a href="<?php echo ($v["url"]); ?>"><?php echo ($v["column_name"]); ?></a><?php endif; ?>	
				</td>
				
                <td align="center"><?php echo ($v["model_name"]); ?></td>
				
                <td><input type="text" name="<?php echo ($v["id"]); ?>" value="<?php echo ($v["column_sort"]); ?>" /></td>
				<td><?php if($v['column_status']==0): ?>是<?php else: ?><span>否</span><?php endif; ?></td>
                <td>
				<a href="/tuzicms/index.php/manage/category/add/id/<?php echo ($v["id"]); ?>">添加子栏目</a>

                
                <a href="/tuzicms/index.php/manage/category/edit/id/<?php echo ($v["id"]); ?>">修改</a>
                <a href="/tuzicms/index.php/manage/category/delete/id/<?php echo ($v["id"]); ?>" onclick="return confirm('是否确定删除?')">删除</a>
				</td>
            </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>	
        </table>

    </form>
    </div>
</div>

<div style="height:50px;"></div>
<div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="http://www.tuzicms.com" target="_blank">TuziCMS</a>企业网站内容管理系统</div>
                <div class="fr"><?php echo (C("setting.Version")); ?></div>
            </div>
</div>
</body>
</html>