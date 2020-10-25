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
	subform('/tuzicms/index.php/manage/banner/delall');
   }
  }
 </script>

<table width="100%" height="31px" border="0" cellpadding="0" cellspacing="0" class="left_topbg" id="table2">
      <tr>
	  <?php if($ifid==not): ?><td height="31"><div class="titlebt">查询结果</div></td>
	 <?php else: ?>
        <td height="31"><div class="titlebt">栏目广告</div></td><?php endif; ?>
      </tr>
    </table>

<div class="main">

    
    <div class="list">    
    <form action="/tuzicms/index.php/manage/banner/sortcate" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                
                <th>栏目编号</th>
                <th>栏目名称</th>
				<th>附件预览</th>
				<th>附件大小</th>            
				<th>开启状态</th>
                <th>操作</th>
            </tr>
			<?php if(empty($vlist)): ?><tr>
			<td colspan="10"><div align="center">本区域暂无数据显示...</div></td>
			</tr>
			<?php else: ?> 
			<?php if(is_array($vlist)): $i = 0; $__LIST__ = $vlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr style="height:95px;">
                
                <td><?php echo ($v["id"]); ?></td>
                <td><?php echo ($v["column_name"]); ?></td>
				
				
				
				<td>
				<?php if($v['column_images']): ?><a href="/tuzicms/Uploads/<?php echo ($v["column_images"]); ?>" target="_blank"><img src="/tuzicms/Uploads/<?php echo ($v["column_images"]); ?>" width="150px" height="50px" border="0" /></a>
				<?php else: ?>
				<img src="/tuzicms/App/Manage/View/Default/Images/nopic.jpg" width="150px" height="50px" border="0" /><?php endif; ?>
				
				
				</td>
				<td><?php echo ($v["column_imgsize"]); ?></td>
                <td>
				<?php if($v['column_ifimg']==1): ?><span style="color:#FF0000">开启</span>
				<?php else: ?>
				不开启<?php endif; ?>
				</td>
                <td>
                    <a href="/tuzicms/index.php/manage/banner/edit/id/<?php echo ($v["id"]); ?>">修改</a>
					
					<?php if($v['column_ifimg']==1): ?><a href="/tuzicms/index.php/manage/banner/ifedit/id/<?php echo ($v["id"]); ?>">关闭</a>
					<?php else: ?>
					<a href="/tuzicms/index.php/manage/banner/ifedit/id/<?php echo ($v["id"]); ?>">开启</a><?php endif; ?>
                    
				</td>
            </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?>
        </table>
    </form>
<div class="green-black"><?php echo ($page); ?>总共<?php echo ($count); ?>条记录</div>
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