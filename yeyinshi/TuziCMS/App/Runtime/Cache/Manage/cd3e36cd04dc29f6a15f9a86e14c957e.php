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
        <td height="31"><div class="titlebt">修改轮播广告</div></td>
      </tr>
    </table>

<div class="main">
    
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="/tuzicms/index.php/manage/advert/do_edit" enctype="multipart/form-data">
		
		<dl>
			<dt> 所属分类：</dt>
			<dd>
				<select name="advert_nav">
				<option value="" >==请选择分类==</option>
					<?php if(is_array($cate)): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>" <?php if($v['advert_nav'] == $vo['id']): ?>selected="selected"<?php endif; ?>><?php echo ($vo["adnav_name"]); ?>[<?php echo ($vo["id"]); ?>]</option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</dd>
		</dl>
		
		<dl>
			<dt> 附件名称：</dt>
			<dd>
				<input type="text" name="advert_name" class="inp_w250" value="<?php echo ($v["advert_name"]); ?>" />
			</dd>
		</dl>
		
		<dl>
			<dt> 附件备注：</dt>
			<dd>
				<input type="text" name="advert_remark" class="inp_w250" value="<?php echo ($v["advert_remark"]); ?>" />
			</dd>
		</dl>
		
		<dl>
			<dt> 图片链接：</dt>
			<dd>
				<input type="text" name="advert_url" class="inp_w250" value="<?php echo ($v["advert_url"]); ?>" />
				&nbsp;注:链接带http://
			</dd>
			
		</dl>
		
		<dl>
			<dt> 排序：</dt>
			<dd>
				<input type="text" name="advert_sort" class="inp_w250" value="<?php echo ($v["advert_sort"]); ?>" />
			</dd>
		</dl>
		
		<dl>
			<dt> 显示状态：</dt>
			<dd>
				<label><input type="radio" name="advert_show" value="0" <?php if(0 == $v['advert_show']): ?>checked="checked"<?php endif; ?>/>显示</label>
				<label><input type="radio" name="advert_show" value="1" <?php if(1 == $v['advert_show']): ?>checked="checked"<?php endif; ?>/>隐藏</label>
				
			</dd>
		</dl>
		
		<dl>
			<dd></dd>
		</dl>
				
		<dl>
			<dt> 图片附件：</dt>
			<dd>
				<input name="advert_image" id="image" type="file" style="height:30px; margin-top:-18px;"/>
				<a href="/tuzicms/Uploads/<?php echo ($v["advert_image"]); ?>" target="_blank"><img src="/tuzicms/Uploads/<?php echo ($v["advert_image"]); ?>" width="180px" height="50px" border="0" /></a>
			</dd>
		</dl>
		
		<div class="form_b">
			<input type="hidden" name="id" value="<?php echo ($v["id"]); ?>" />	
			<input type="submit" class="btn_blue" id="submit" value="提 交">
		</div>
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