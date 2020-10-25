<?php if (!defined('THINK_PATH')) exit();?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
	subform('/tuzicms/index.php/manage/user/delall');
   }
  }
 </script>

<table width="100%" height="31px" border="0" cellpadding="0" cellspacing="0" class="left_topbg" id="table2">
      <tr>
        <td height="31"><div class="titlebt">会员管理</div></td>
      </tr>
    </table>

<div class="main">

    <div class="operate">
        <div class="left">
            <input type="button" onclick="window.open('/tuzicms/index.php/manage/user/add','main')" target="main" class="btn_blue" value="添加">
				<input class="btn_blue" type="button" onClick="return clear_del();" value="删除" />      
        </div>
        <?php if(ACTION_NAME == "index"): ?><div class="left_pad">
            <form method="get" action="/tuzicms/index.php/manage/user/search">
                <input type="text" name="keyword" title="关键字" class="inp_default" value="">
                <input type="hidden" name="formhash" value="231cb4d8" />
                <input type="submit" class="btn_blue" value="查  询">
            </form>
        </div><?php endif; ?>
    </div>
    <div class="list">    
    <form action="<?php echo U(GROUP_NAME.'/Member/delAll');?>" method="post" id="form_do" name="form_do">
        <table width="100%">
            <tr>
                <th><input  name="chkall" type="checkbox" id="chkall" onclick="selectall(this.form)"></th>
                <th>编号</th>
                <th>邮箱</th>
                <th>昵称</th>
                <th>注册时间</th>
                <th>登录时间</th>
                <th>登录ip</th>
				<th>登录次数</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
			<?php if(empty($vlist)): ?><tr>
			<td colspan="10"><div align="center">本区域暂无数据显示...</div></td>
			</tr>
			<?php else: ?> 
			
			<?php if(is_array($vlist)): $i = 0; $__LIST__ = $vlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><tr>
                <td><input type="checkbox" name="id[]" value="<?php echo ($v["id"]); ?>" ></td>
                <td><?php echo ($v["id"]); ?></td>
                <td><?php echo ($v["user_email"]); ?></td>
                <td><?php echo ($v["user_name"]); ?></td>
                <td><?php echo (date('Y-m-d H:i:s',$v["user_rsdate"])); ?></td>
                <td><?php echo (date('Y-m-d H:i:s',$v["user_date"])); ?></td>
                <td><?php echo ($v["user_ip"]); ?></td>
				<td><?php echo ($v["user_login"]); ?></td>
                <td><?php if($v['user_ok']): ?>激活<?php else: ?><span style="color:#FF0000">未激活</span><?php endif; ?></td>
                <td>
                    <a href="/tuzicms/index.php/manage/user/detail/id/<?php echo ($v["id"]); ?>">详情</a>
                    <a href="/tuzicms/index.php/manage/user/edit/id/<?php echo ($v["id"]); ?>">修改密码</a>
                    <a href="/tuzicms/index.php/manage/user/del/id/<?php echo ($v["id"]); ?>" onclick="return confirm('是否确定删除?')">删除</a>
				</td>
            </tr><?php endforeach; endif; else: echo "" ;endif; endif; ?> 
			
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