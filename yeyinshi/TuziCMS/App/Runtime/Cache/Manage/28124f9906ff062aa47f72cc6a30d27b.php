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
        <td height="31"><div class="titlebt">编辑文章</div></td>
      </tr>
    </table>
<div class="main"> 
	<div class="form">
		<form method='post' id="form_do" name="form_do" action="<?php echo U('Article/do_edit');?>" enctype="multipart/form-data">
		<dl>
			<dt> 所属栏目：</dt>
			<dd>
				<select name="nv_id">
					<?php if(is_array($Columnlist)): $i = 0; $__LIST__ = $Columnlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><option value="<?php echo ($v["id"]); ?>" <?php if($cate['nv_id'] == $v['id']): ?>selected="selected"<?php endif; ?>>
					<?php echo ($v["html"]); echo ($v["column_name"]); ?>
					</option><?php endforeach; endif; else: echo "" ;endif; ?>
				</select>
			</dd>
		</dl>
		<dl>
			<dt> 自定义属性：</dt>
			<dd class="admin_zhuti">
					<?php if(is_array($flagtypelist)): $i = 0; $__LIST__ = $flagtypelist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?><label><input type="checkbox" name='access[]' value='<?php echo ($v["id"]); ?>'  <?php if($v['access']==1): ?>checked="checked"<?php endif; ?> />
					<span style="color:<?php echo ($v["attr_color"]); ?>"><?php echo ($v["attr_name"]); ?></span>
					</label>&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
			</dd>
		</dl>
		<div class="pic_suolue">
		<dl>
			<dt> 缩略图：</dt>
			<dd>
				<div class="litpic_show">
				    <div style="float:left;">
				    <input type="text" class="inp_w250" name="pic" id="litpic"  value="<?php echo ($cate["news_pic"]); ?>" />
				    </div>
						<div>
						<input name="news_pic" id="image" type="file" style="height:30px;" />
				    </div>
				    <div class="litpic_tip"></div>
				</div>
			</dd>
		</dl>
		<dl>
			<dt></dt>
			<dd>
				<?php if($cate['news_pic']): ?><img src="<?php echo ($cate["news_showpic"]); ?>" width='120' /><?php endif; ?>
			</dd>
		</dl>
		</div>
		<dl>
			<dt> 点击次数：</dt>
			<dd>
				<input type="text" name="news_hits" class="inp_w250" value="<?php echo ($cate["news_hits"]); ?>" />
			</dd>
		</dl>
		<dl>
			<dt> 文章排序：</dt>
			<dd>
				<input type="text" name="news_sort" class="inp_w250" value="<?php echo ($cate["news_sort"]); ?>" />
				&nbsp;&nbsp;注:越小越靠前
			</dd>
		</dl>
		
		
		<dl>
			<dt> 标题：</dt>
			<dd>
				<input type="text" name="news_title" class="inp_large" value="<?php echo ($cate["news_title"]); ?>" />
			</dd>
		</dl>
		
		<dl>
			<dt> 关键字：</dt>
			<dd>
				<input type="text" name="news_keywords" class="inp_w250" value="<?php echo ($cate["news_keywords"]); ?>" />&nbsp;&nbsp;注:多个关键字用英文逗号","相隔
			</dd>
		</dl>
		
				
		<!--载入kindeditor编辑器开始-->
		<script type="text/javascript" charset="utf-8" src="/tuzicms/Data/kindeditor/kindeditor.js"></script>
		<script charset="utf-8" src="/tuzicms/Data/kindeditor/lang/zh_CN.js"></script>
		<script language="javascript">
		var editor;
		KindEditor.ready(function(K) {
		editor = K.create('#intro');
		// editor = K.create('#editor_id');多个
		});
		</script>
		<!--<textarea id="editor_id" name="content" style="width:280px;height:160px;"></textarea>-->
		<!--载入kindeditor编辑器结束-->
		
		<dl>
			<dt> 内容：</dt>
			<dd>
				
		<div>
		<textarea id="intro" name="news_content" style="width:900px;height:400px;"/><?php echo ($cate["news_content"]); ?></textarea>
		</div>
				
				
			</dd>
		</dl>
		

		
		<dl>
			<dt> 作者：</dt>
			<dd>
				<input type="text" name="news_author" class="inp_w250" value="<?php echo ($cate["news_author"]); ?>" /><span class="tip"></span>
			</dd>
		</dl>			
			
		
		<div class="form_b">
			<input type="hidden" name="id" value="<?php echo ($cate["id"]); ?>" />		
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