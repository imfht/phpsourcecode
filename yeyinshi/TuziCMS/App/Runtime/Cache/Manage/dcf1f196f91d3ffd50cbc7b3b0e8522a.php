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

<table width="100%" height="31px" border="0" cellpadding="0" cellspacing="0" class="left_topbg" id="table2">
      <tr>
        <td height="31"><div class="titlebt">广告分类管理</div></td>
      </tr>
    </table>

<div class="main">
    <div class="list">    
    <form action="{:U(GROUP_NAME.'/Model/sort')}" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th>编号</th>
                <th>广告栏目名称</th>
                <th>添加时间</th>
                <th>操作</th>
            </tr>
			<?php if(is_array($vlist)): foreach($vlist as $key=>$v): ?><tr>
                <td><?php echo ($v["id"]); ?></td>
                <td><?php echo ($v["adnav_name"]); ?></td>
				<td><?php echo (date('Y-m-d H:i:s',$v["adnav_time"])); ?></td>
                <td>
                <a href="/tuzicms/index.php/manage/advert/adnavedit/id/<?php echo ($v["id"]); ?>">修改</a>
				<a href="/tuzicms/index.php/manage/advert/adnavdel/id/<?php echo ($v["id"]); ?>" onClick="return confirm('是否确定删除?')">删除</a>
                
				</td>
            </tr><?php endforeach; endif; ?>
        </table>
        <div class="green-black"><?php echo ($page); ?>总共<?php echo ($count); ?>条记录</div>
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