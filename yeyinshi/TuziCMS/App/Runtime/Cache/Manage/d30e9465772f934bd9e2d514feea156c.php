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
	subform('/tuzicms/index.php/manage/advert/delall');
   }
  }
 </script>

<table width="100%" height="31px" border="0" cellpadding="0" cellspacing="0" class="left_topbg" id="table2">
      <tr>
	  <?php if($ifid==not): ?><td height="31"><div class="titlebt">查询结果</div></td>
	 <?php else: ?>
        <td height="31"><div class="titlebt">营销广告</div></td><?php endif; ?>
      </tr>
    </table>

<div class="main">

    <div class="operate">
        <div class="left">
            <input type="button" onclick="window.open('/tuzicms/index.php/manage/advert/add/nav/<?php echo ($nav); ?>','main')" target="main" class="btn_blue" value="添加">
			<input type="button" onclick="window.open('/tuzicms/index.php/manage/advert/addsort','main')" target="main" class="btn_blue" value="添加分类">
			<input type="button" onclick="window.open('/tuzicms/index.php/manage/advert/adnav','main')" target="main" class="btn_blue" value="分类管理">
			<input class="btn_blue" type="button" onClick="return clear_del();" value="删除" />
			<input type="button" onclick="javascript:document.form_do.submit();"  class="btn_blue" value="更新排序">      
        </div>
        <?php if(ACTION_NAME == "index"): ?><div class="left_pad">
            <form method="post" action="/tuzicms/index.php/manage/advert/search">
                <input type="text" name="advert_name" title="关键字" class="inp_default" value="<?php echo ($keyword); ?>">
                <input type="submit" class="btn_blue" value="查  询">
            </form>
        </div><?php endif; ?>
    </div>
    <div class="list">    
    <form action="/tuzicms/index.php/manage/advert/sortcate/nav/<?php echo ($nav); ?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input  name="chkall" type="checkbox" id="chkall" onclick="selectall(this.form)"></th>
                <th>编号</th>
				<th>所属分类</th>
                <th>附件名称</th>
				
				<th>附件预览</th>
                <th>附件大小</th>            
				<th>上传时间</th>
				<th>排序</th>
                <th>显示状态</th>
                <th>操作</th>
            </tr>
			<?php if(empty($vlist)): ?><tr>
			<td colspan="10"><div align="center">本区域暂无数据显示...</div></td>
			</tr>
			<?php else: ?> 
			<?php if(is_array($vlist)): $i = 0; $__LIST__ = $vlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr style="height:95px;">
                <td><input type="checkbox" name="id[]" value="<?php echo ($v["id"]); ?>" ></td>
                <td><?php echo ($v["id"]); ?></td>
				<td><?php echo ($v["adnav_name"]); ?>[id=<?php echo ($v["advert_nav"]); ?>]</td>
                <td><?php echo ($v["advert_name"]); ?></td>
				<td><a href="/tuzicms/Uploads/<?php echo ($v["advert_image"]); ?>" target="_blank"><img src="/tuzicms/Uploads/<?php echo ($v["advert_image"]); ?>" width="150px" height="50px" border="0" /></a></td>
                <td><?php echo ($v["advert_size"]); ?></td>               
				 <td><?php echo (date('Y-m-d H:i:s',$v["advert_time"])); ?></td>
				 <td><input type="text" name="<?php echo ($v["id"]); ?>" value="<?php echo ($v["advert_sort"]); ?>" /></td>
                <td>
				<?php if($v['advert_show']==1): ?><span style="color:#FF0000">隐藏</span>
				<?php else: ?>
				显示<?php endif; ?>
				</td>
                <td>
                    <a href="/tuzicms/index.php/manage/advert/edit/id/<?php echo ($v["id"]); ?>">修改</a>
                    <a href="/tuzicms/index.php/manage/advert/del/id/<?php echo ($v["id"]); ?>" onclick="return confirm('是否确定删除?')">删除</a>
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