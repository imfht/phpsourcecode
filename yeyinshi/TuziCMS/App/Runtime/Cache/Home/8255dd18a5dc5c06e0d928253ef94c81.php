<?php if (!defined('THINK_PATH')) exit();?>﻿
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
 <SCRIPT LANGUAGE="JavaScript">
function mobile_device_detect(url)
{

        var thisOS=navigator.platform;

        var os=new Array("iPhone","iPod","iPad","android","Nokia","SymbianOS","Symbian","Windows Phone","Phone","Linux armv71","MAUI","UNTRUSTED/1.0","Windows CE","BlackBerry","IEMobile");

 for(var i=0;i<os.length;i++)
        {

 if(thisOS.match(os[i]))
        {   
  window.location=url;
 }
  
 }


 //因为相当部分的手机系统不知道信息,这里是做临时性特殊辨认
 if(navigator.platform.indexOf('iPad') != -1)
        {
  window.location=url;
 }

 //做这一部分是因为Android手机的内核也是Linux
 //但是navigator.platform显示信息不尽相同情况繁多,因此从浏览器下手，即用navigator.appVersion信息做判断
  var check = navigator.appVersion;

  if( check.match(/linux/i) )
          {
   //X11是UC浏览器的平台 ，如果有其他特殊浏览器也可以附加上条件
   if(check.match(/mobile/i) || check.match(/X11/i))
                 {
   window.location=url;
   }  
 }

 //类in_array函数
 Array.prototype.in_array = function(e)
 {
  for(i=0;i<this.length;i++)
  {
   if(this[i] == e)
   return true;
  }
  return false;
 }
} 

mobile_device_detect("http://www.tuzicms.cn/index.php/mobile");

</SCRIPT>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" type="image/ico" href="/favicon.ico">
<title><?php echo ($title); ?> - Powered by TuziCMS</title>
<meta name="keywords" content="<?php echo ($keywords); ?>" />
<meta name="description" content="<?php echo ($description); ?>" />
<script type="text/javascript">
	var Public = "/tuzicms/Public/Home/Default";
</script>
<script  type="text/javascript" src="/tuzicms/Public/Home/Default/js/jquery_min.js"></script>
<script  type="text/javascript" src="/tuzicms/Public/Home/Default/js/index.js"></script>
<script  type="text/javascript" src="/tuzicms/Public/Home/Default/js/topdb.js"></script>
<!--[if IE 6]><script src="/tuzicms/Public/Home/Default/js/png.js"></script><![endif]-->
<link rel="stylesheet" type="text/css" href="/tuzicms/Public/Home/Default/css/common.css" media="all">
<link rel="stylesheet" type="text/css" href="/tuzicms/Public/Home/Default/css/index.css" media="all">
</head>
<body>

<script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1256162028'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s11.cnzz.com/z_stat.php%3Fid%3D1256162028%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
<!-- 页头 -->
﻿<!--[if IE 6]>
<script src="/tuzicms/Public/Home/Default/js/DD_belatedPNG_0.0.8a-min.js" language="javascript" type="text/javascript"></script>
<script>
  DD_belatedPNG.fix('#top_logo');   /* string argument can be any CSS selector */
</script>
<![endif]-->
<script type="text/javascript">
$(function(){
	var $chkurl = '<?php echo U('Common/loginChk');?>';
	$.get($chkurl,function(data){
		//alert(data);
		if (data.status == 1) {
			$('#top_login_ok').show();
			$('#top_login_no').hide();
			//$('#top_login_ok').find('span');
			$('#top_login_ok>span').html('欢迎您，'+data.nickname);
		}else {			
			$('#top_login_ok').hide();
			$('#top_login_no').show();
		}
	},'json');	
});
</script>
<div class="header" >
	<div class="column">
        <div class="top-bar">
		<div class="nav_left">
		欢迎您访问<?php echo (C("setting.Software")); ?>演示网站！
		</div>
		<div class="nav_right"> 

		<div id="top_login_no">
		<img src="/tuzicms/Public/Home/Default/images/register.png"  class="top_images" />
		<a href="/tuzicms/index.php/user/register">会员注册</a>
		<img src="/tuzicms/Public/Home/Default/images/loginin.png" class="top_images" />	
		<a href="/tuzicms/index.php/user/login">会员登录</a>	
		</div>
		<div id="top_login_ok" style="display:none;">
		<span>欢迎您， </span>
		<img src="/tuzicms/Public/Home/Default/images/login.png" class="top_images" />
		<a href="/tuzicms/index.php/user/index">会员中心</a>
		<img src="/tuzicms/Public/Home/Default/images/loginout.png" class="top_images" />
		<a href="/tuzicms/index.php/user/do_out">安全退出</a>
		</div>	

		</div>   
        </div>
	</div>
	<div class="column" style="clear:both">
    	<div class="logo"><a href="/tuzicms/index.php"><img alt="TuziCMS-企业网站内容管理系统" height="62" src="/tuzicms/Public/Home/Default/images/logo.png" /></a></div>			
        <div class="nav">
            <ul id="menu">
						
<?php
 $_nav_m=D('Column')->order("column_sort")->field('id,f_id,column_name,column_ename,column_url,column_type,column_sort,column_status,column_link')->where("column_status=0")->relation(true)->select(); $_nav_m=Common\Lib\Category::unlimitedForLayer($_nav_m); $modlu='/tuzicms/index.php/home/product/detail'; strpos($modlu, "mobile"); if (strpos($modlu, "mobile")==''){ foreach($_nav_m as $k3 => $v3){ if($v3['column_link']==1){ $_nav_m[$k3]['url'] = '/tuzicms/index.php'.'/'.$v3['column_url']; } if($v3['column_link']==2){ $_nav_m[$k3]['url'] = '/tuzicms/index.php'.'/'.$v3['column_ename']; } if($v3['column_link']==0){ $_nav_m[$k3]['url'] = '/tuzicms/index.php'.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id']; } } }else { foreach($_nav_m as $k3 => $v3){ if($v3['column_link']==1){ $_nav_m[$k3]['url'] = '/tuzicms/index.php'.'/'.'mobile'.'/'.$v3['column_url']; } if($v3['column_link']==2){ $_nav_m[$k3]['url'] = '/tuzicms/index.php'.'/'.'mobile'.'/'.$v3['column_ename']; } if($v3['column_link']==0){ $_nav_m[$k3]['url'] = '/tuzicms/index.php'.'/'.'mobile'.'/'.$v3['url'].'/'.group.'/'.'id'.'/'.$v3['id']; } } } foreach($_nav_m as $autoindex => $_nav_v): extract($_nav_v); ?><li id="dr_nav_<?php echo ($id); ?>">
				<a href="<?php echo ($url); ?>" class="nav-a"><?php echo ($column_name); ?></a>
				</li><?php endforeach;?>
            </ul>
        </div>
    </div>
</div>



<!--@end 页头 -->
<div class="cont-bg">
		<!-- 全局导航 -->
		﻿<div class="quanju_nav">
<div class="quan_hd">
					<p class="crumb">
						<em></em> <a href="/tuzicms/index.php">网站首页</a> &gt;
						<?php if($ifid==not): ?><a href="/tuzicms/index.php/Guestbook">在线留言</a>
						<?php else: ?>
						<?php if(is_array($topnavlist)): foreach($topnavlist as $key=>$nav): ?><a href="<?php echo ($nav["url"]); ?>"><span><?php echo ($nav["column_name"]); ?></span></a>
						<?php if($key != $last): ?>&gt;<?php endif; endforeach; endif; ?>
							<?php if($acion_name==detail): ?>&gt;
							<?php else: endif; ?>
						<span style="color:rgb(68, 68, 68);"><?php echo ($v["news_title"]); ?></span><?php endif; ?>			
					</p>			
</div>
<div style="float:right; height:50px; line-height:50px;">
<div class="left_pad">
            <form method="get" action="<?php echo U('Search/result');?>">
                <input type="text" name="keyword" title="关键字" class="inp_default" value="<?php echo ($keyword); ?>">
                <input type="submit" class="btn_blue" value="查  询">
            </form>
</div>
</div>
</div>

		<!--@end 全局导航 -->
	<div class="column article-page">
        <div class="cont_detail">
			<div class="detail_title">
            <h2 class="title"><?php echo ($v["news_title"]); ?></h2>
			</div>	
			<div class="detail_date">
			<var><?php echo (date('Y-m-d H:i:s', $v["news_addtime"])); ?></var> &nbsp;&nbsp;<var>点击数：<script type='text/javascript' src='<?php echo U("/Common/clicknum",array("id" => $v["id"]));?>'></script></var> &nbsp;&nbsp;<var>发布者:<?php echo ($v["news_author"]); ?>  </var>
			</div>
			<div class="detail_content">
			<?php echo ($v["news_content"]); ?>
			</div>	
        <div class="detail_about">
          <h2>相关阅读</h2>
          ﻿<div class="ov">
            <ul class="txt-list-a lh-30 cor-333 fz-14 ov">
							<?php if(is_array($vrelated)): foreach($vrelated as $key=>$v): ?><li>
							<div style="float:left">
							<a href="<?php echo ($v["url"]); ?>" title="<?php echo ($v["news_title"]); ?>" target="_blank"><?php echo ($v["news_title"]); ?></a>
							</div>
							<div style="float:right">
							<span class="date"><?php echo (date('Y-m-d H:i:s',$v["news_addtime"])); ?></span>
							</div>
							</li><?php endforeach; endif; ?>
             </ul>
</div>
        </div>	  
        </div>
    </div>
</div>
<!-- 页脚 -->
﻿
<div class="footer ov fz-12" style="clear:both">
	<div class="link">
	<span><b>友情链接:</b></span>
			<?php
 $_link_m=D('Link')->order('link_sort ')->limit("32")->where("link_show=0")->select(); foreach($_link_m as $_link_v): extract($_link_v); ?><a href="<?php echo ($link_url); ?>" target="_blank" title="<?php echo ($link_name); ?>"><?php echo ($link_name); ?></a><?php endforeach;?>
</div>
</div>
<!-- 页脚 -->
<div style="height:60px; line-height:60px; text-align:center; background:#F5F5F5;">
<div id="ft" class="w cl">

<em>&copy; 2014-2015 <strong><a href="http://www.Yejiao.net" target="_blank" style="color:#4CB32E;">Yejiao Team</a></strong> 版权所有</em>
 &nbsp; &nbsp;
<em>本站由<strong><a href="http://www.tuzicms.com" target="_blank" style="color:#4CB32E;"> <?php echo (C("setting.Software")); ?> </a></strong>强力驱动</em>
</div>
</div>

<?php
 $_link_m=D('Kefu')->order("id")->limit("0,3")->select(); foreach($_link_m as $_link_v): extract($_link_v); if($kefu_if==1): ?><!--在线客服start-->
<div class="tuzikf">
	<div class="slide_min"></div>
    <div class="slide_box" style="display:none;">
    	<h2><img src="/tuzicms/Public/Home/Default/images/slide_box.jpg" /></h2>
        <p><a title="点击这里给我发消息" href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?php echo ($kefu_qq); ?>&amp;site=www.cactussoft.cn&amp;menu=yes" target="_blank"><img src="/tuzicms/Public/Home/Default/images/qqweb.gif"></a></p>
        <p>
        	<img src="/tuzicms/Public/Home/Default/images/qrcode.jpg" class="weixin"  width="120" height="120"/><br />
        	<b>客户服务热线</b><br />
        	<?php echo ($kefu_tel); ?>
        </p>
        <span><a href="/tuzicms/index.php/guestbook">给我们留言</a></span>
    </div>
</div>
<script>
$(function(){
	var thisBox = $('.tuzikf');
	var defaultTop = thisBox.offset().top;
	var slide_min = $('.tuzikf .slide_min');
	var slide_box = $('.tuzikf .slide_box');
	var closed = $('.tuzikf .slide_box h2 img');
	slide_min.on('click',function(){$(this).hide();	slide_box.show();});
	closed.on('click',function(){slide_box.hide().hide();slide_min.show();});
	// 页面滚动的同时，悬浮框也跟着滚动
	$(window).on('scroll',function(){scro();});
	$(window).onload = scro();
	function scro(){
		var offsetTop = defaultTop + $(window).scrollTop()+'px';
		thisBox.animate({top:offsetTop},
		{	duration: 600,	//滑动速度
	     	queue: false    //此动画将不进入动画队列
	     });
	}
});
</script>
<!--在线客服end-->
<?php else: endif; endforeach;?>
<div id="FloatMenu">
<a href="javascript:;" rel="external nofollow" id="totop" title="返回顶部">返回顶部</a>
</div>
<!--@end 页脚 -->
</body>
</html>