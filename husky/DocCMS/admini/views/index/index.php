<div class="noun">
  <div id="am_menu">
    <ul>
      <li class="amli hover" onclick="dex(0)">功能菜单</li>
      <li class="amli" onclick="dex(1)">最新新闻</li>
      <li class="amli" onclick="dex(2)">站点信息</li>
      <li class="amli" onclick="dex(3)">网站统计</li>
      <li class="amli" onclick="dex(4)">关于稻壳</li>
      <li class="doooc"><a target="_blank" href="http://www.doooc.com"><img src="images/doooc.jpg" /></a></li>
    </ul>
  </div>
  <div id="am_main">
    <div class="amcon func" id="block">
      <ul>
        <li> <a href="./index.php?m=system&s=managechannel">设计导航菜单</a>
          <p>从这里开始建设网站，给网站设定栏目。</p>
        </li>
        <li> <a href="./index.php?m=system&s=changeskin">模板管理</a>
          <p>选择或修改您喜欢的网站模板样式。</p>
        </li>
        <li> <a href="./index.php?m=system&s=options">站点设置</a>
          <p>设定网站基本信息和各项参数配制。</p>
        </li>
      </ul>
      <ul>
        <li> <a href="./index.php?m=system&s=userinfo">用户管理</a>
          <p>在这里创建您的用户账号以及分配用户权限。</p>
        </li>
        <li> <a href="./index.php?m=system&s=managemodel">模块管理</a>
          <p>在这里管理和查看模块属性。</p>
        </li>
        <li> <a href="./index.php?m=system&s=bakup">数据库管理</a>
          <p>垃圾数据清理，数据库备份、恢复、管理。</p>
        </li>
      </ul>
      <ul>
        <li> <a href="./index.php?m=system&s=flashoptions">广告管理</a>
          <p>控制首页焦点图中的轮换图片。</p>
        </li>
        <li> <a href="./index.php?m=system&s=managehtml">静态化设置</a>
          <p>网站页面静态化管理。</p>
        </li>
        <li> <a href="./index.php?m=system&s=manageresource">Ftp资源管理</a>
          <p>利用在线Ftp功能帮助清理垃圾数据和文件。</p>
        </li>
      </ul>
    </div>
    <div class="amcon amnew">
      <ul>
        <?php echo $news_list_content;?>
      </ul>
    </div>
    <style>
	.table_list{ border:2px solid #FFFFFF; border-width:2px 0 0 2px; margin:15px 0; background:#f8f8ff; }
	.table_list td,.table_list th{ padding:3px 5px; line-height:16px;border:2px solid #FFFFFF; border-width:0 1px 1px 0; text-align: left;}
	li {line-height: 22px; list-style:none;font-size: 12px;}
	.copy {line-height: 22px;font-family: PMingLiU, Verdana, serif;font-size: 12px;}
	.yellow{ color:#FF9900;}
	.red{ color:#FF0000;}
	.right{ padding-left:15px; background:url(images/right.gif) left center no-repeat;}
	.wrong{ padding-left:15px; background:url(images/wrong.gif) left center no-repeat;}
	.dis{ display:none}
	.red{ color:#F00}
	.green{ color:#690}
	.yelo{ color:#F60}
	.spacecount{ color:#000}
.webstatistic { width:98%; height:134px; margin:10px 0 0 10px; }
.webstatistic li { height:44px; float:left; width:16.45%; }
.webstatistic .stactop { border-top:1px solid #ddd; border-left:1px solid #ddd; }
.webstatistic li span { display:block; height:20px; text-align:center; line-height:20px; color:#666; }
.webstatistic li .atsn { background:#efefef; margin:2px 0 2px 2px; }
.webstatistic li .numb { background:#f8f8f8; margin:0 0 0 2px; }
.webstatistic .statop { border-top:1px solid #ddd; }
.webstatistic .statopc { border-top:1px solid #ddd; border-right:1px solid #ddd; padding-right:2px; }
.webstatistic .staleft { border-left:1px solid #ddd; }
.webstatistic .staright { border-right:1px solid #ddd; padding-right:2px; }
.webstatistic .stacbot { border-left:1px solid #ddd; border-bottom:1px solid #ddd; padding-bottom:2px; }
.webstatistic .stabot { border-bottom:1px solid #ddd; padding-bottom:2px; }
.webstatistic .stabotc { border-right:1px solid #ddd; border-bottom:1px solid #ddd; padding-bottom:2px; padding-right:2px; }
.main h4{ padding:20px 0; font-size:16px; text-align:center; font-weight:normal; color:#FDD90B;}
.main p{ padding:6px 0; text-indent:24px;}		
.main{ font-size:13px; font-family:"微软雅黑"; line-height:26px; padding:15px;}
.main a{ color:#FDD90B;}
.adintro{ margin:15px 0; border:1px dotted #ddd; padding:10px; background:#fff; color:#666;}
	</style>
    <?php
    $PHP_GD = '';
	if(extension_loaded('gd'))
	{
		if(function_exists('imagepng')) $PHP_GD .= '.png';
		if(function_exists('imagejpeg')) $PHP_GD .= ' .jpg';
		if(function_exists('imagegif')) $PHP_GD .= ' .gif';
	}
	?>
    <div class="amcon">
      <table width="100%" cellpadding="0" cellspacing="0" class="table_list">
        <tr>
          <th>检查项目</th>
          <th>当前环境</th>
          <th>建议环境</th>
          <th>功能影响</th>
        </tr>
        <tr>
          <td>操作系统</td>
          <td><?php echo php_uname();?></td>
          <td>Windows_NT/Linux/Freebsd</td>
          <td><span class="yellow">√</span></td>
        </tr>
        <tr>
          <td>web 服务器</td>
          <td><?php echo $_SERVER['SERVER_SOFTWARE'];?></td>
          <td>Apache/IIS</td>
          <td><span class="yellow">√</span></td>
        </tr>
        <tr>
          <td>php 版本</td>
          <td><?php echo phpversion();?></td>
          <td>php 5.0 及以上</td>
          <td><?php if(phpversion() >= '5.0.0'){ ?>
            <span class="yellow">√</span>
            <?php }else{ ?>
            <span class="red">无法正常使用</span>
            <?php }?></td>
        </tr>
        <tr>
          <td>mysql 版本</td>
          <td><?php global $db;echo $db->get_var('SELECT VERSION()');?></td>
          <td>mysql 5.0 及以上</td>
          <td><?php if((float)$db->get_var('SELECT VERSION()') >= '5.0'){ ?>
            <span class="yellow">√</span>
            <?php }else{ ?>
            <span class="red">无法正常使用</span>
            <?php }?></td>
        </tr>
        <tr>
          <td>mysql 扩展</td>
          <td><?php if(extension_loaded('mysql')){ ?>
            √
            <?php }else{ ?>
            ×
            <?php }?></td>
          <td>建议开启</td>
          <td><?php if(extension_loaded('mysql')){ ?>
            <span class="yellow">√</span>
            <?php }else{ ?>
            <span class="red">无法正常使用</span>
            <?php }?></td>
        </tr>
        <tr>
          <td>gd 扩展</td>
          <td><?php if($PHP_GD){ ?>
            √ （支持 <?php echo $PHP_GD;?>）
            <?php }else{ ?>
            ×
            <?php }?></td>
          <td>建议开启</td>
          <td><?php if($PHP_GD){ ?>
            <span class="yellow">√</span>
            <?php }else{ ?>
            <span class="red">不支持缩略图和水印</span>
            <?php }?></td>
        </tr>
        <tr>
          <td>ob_start 缓存</td>
          <td><?php if(function_exists(ob_start)){ ?>
            √ （支持网站静态化）
            <?php }else{ ?>
            <span class="red">×</span>
            <?php }?></td>
          <td>建议开启</td>
          <td><?php if(function_exists(ob_start)){ ?>
            <span class="yellow">√</span>
            <?php }else{ ?>
            <span class="red">不支持网站静态化</span>
            <?php }?></td>
        </tr>
      </table>
    </div>
    <div class="amcon">
      <ul class="webstatistic">
        <li class="stactop"><span class="atsn">图文模块</span><span class="numb">
          <?php global $db;if($db->get_var("SHOW TABLES LIKE '".TB_PREFIX."article'")==TB_PREFIX.'article')echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."article");?>
          </span></li>
        <li class="statop"><span class="atsn">文章列表</span><span class="numb">
          <?php global $db;if($db->get_var("SHOW TABLES LIKE '".TB_PREFIX."list'")==TB_PREFIX.'list')echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."list");?>
          </span></li>
        <li class="statop"><span class="atsn">图片模块</span><span class="numb">
          <?php global $db;if($db->get_var("SHOW TABLES LIKE '".TB_PREFIX."picture'")==TB_PREFIX.'picture')echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."picture");?>
          </span></li>
        <li class="statop"><span class="atsn">产品模块</span><span class="numb">
          <?php global $db;if($db->get_var("SHOW TABLES LIKE '".TB_PREFIX."product'")==TB_PREFIX.'product')echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."product");?>
          </span></li>
        <li class="statop"><span class="atsn">订单模块</span><span class="numb">
          <?php global $db;if($db->get_var("SHOW TABLES LIKE '".TB_PREFIX."order'")==TB_PREFIX.'order')echo $db->get_var("SELECT count(*) FROM `".TB_PREFIX."order`");?>
          </span></li>
        <li class="statopc"><span class="atsn">google地图</span><span class="numb">
          <?php global $db;if($db->get_var("SHOW TABLES LIKE '".TB_PREFIX."mapshow'")==TB_PREFIX.'mapshow')echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."mapshow");?>
          </span></li>
        <li class="staleft"><span class="atsn">招聘模块</span><span class="numb">
          <?php global $db;if($db->get_var("SHOW TABLES LIKE '".TB_PREFIX."jobs'")==TB_PREFIX.'jobs')echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."jobs");?>
          </span></li>
        <li><span class="atsn">投票系统</span><span class="numb">
          <?php global $db;echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."poll_category");?>
          </span></li>
        <li><span class="atsn">友情链接</span><span class="numb">
          <?php global $db;echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."linkers");?>
          </span></li>
        <li><span class="atsn">视频模块</span><span class="numb">
          <?php global $db;echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."video");?>
          </span></li>
        <li><span class="atsn">留言模块</span><span class="numb">
          <?php global $db;echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."guestbook");?>
          </span></li>
        <li class="staright"><span class="atsn">下载模块</span><span class="numb">
          <?php global $db;echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."download");?>
          </span></li>
        <li class="stacbot"><span class="atsn">列表调用</span><span class="numb">
          <?php global $db;echo $db->get_var("SELECT count(*) FROM ".TB_PREFIX."calllist");?>
          </span></li>
        <li class="stabot"><span class="atsn"></span><span class="numb"></span></li>
        <li class="stabot"><span class="atsn"></span><span class="numb"></span></li>
        <li class="stabot"><span class="atsn"></span><span class="numb"></span></li>
        <li class="stabot"><span class="atsn"></span><span class="numb"></span></li>
        <li class="stabotc"><span class="atsn"></span><span class="numb"></span></li>
      </ul>
    </div>
    <div class="amcon">
      <div class="adintro">
        <h4>为什么叫 DocCms [ 中文名：稻壳Cms ]？</h4>
        <p> 2012年06月01日，原 深喉咙Cms 团队[2006年前曾用名XmlOL技术在线]重组，并决定将旗下原有知名企业建站内容管理系统 深喉咙Cms 进行全新定义，将其在保持原有功能特色基础上进行全新开发，使其更加专业、通用、易用，满足不同使用者的需求。2012年08月08日，经过2个月时间筛选斟酌，正式将新Cms 用名确定为 DocCms ； </p>
        <p> DocCms ，官方将其中文名音译为 " 稻壳Cms "。 doc 为英文单词 " document " 的缩写，可译为 "公文、资料、文档、文件等"，使用过微软公司世界著名的企业办公软件 Office 的人对此都应该不会感到陌生，因为 Office 产品家族中最被人们常用的软件之一 Word 文件的后缀名，即为 " .doc &quot; ，因此我们选择使用 DocCms 作为我们新产品的现用名寓意自然也是不言而喻… </p>
        <div class="picimg" style="height:140px;padding:12px 0;text-align:center;"> <img src="images/about.png" style="float:none;" vspace="0" width="720" border="0" height="128" hspace="0" /> </div>
        <p> 说来也巧，当2012年08月08日团队将此名子确定下来后，第一时间告知了对原深喉咙Cms发展作出过特殊重大贡献者之一的 " 雅风[英文ID：yophoo]，深喉咙Cms用户 " 童靴后，团队负责人 可非 和 雅风 在QQ上的几句闲聊，雅风随口问的新中文名叫什么好？并说不如直接就叫 " 稻壳Cms " 算了(ps：雅风是东北人，可能对稻米比较有感情 ; )，很快得到了 可非 童靴的响应，并将其告知团队其它同事并征求大家意见通过后即确定并很快的注册了 " 稻壳 " 商标。此后还开玩笑问雅风要多少创意版权费时，雅风果也大方的笑说给五十万就够了哈哈，因此我们也希望 稻壳Cms 能有一天在为大家带来方便和价值的同时，能够取得成功，并且 为雅风果 兑现版权费一说 ^^。 </p>
        <h4> DocCms X 能做什么？ </h4>
        <p> [音译：稻壳Cms]，是一款将于2012年11月11日正式发布，定位于为企业、站长、开发者、网络公司、VI策划设计公司、SEO推广营销公司、网站初学者等用户 量身打造的一款全新企业建站、内容管理系统，服务于企业品牌信息化建设，也适应用个人、门户网站建设！ </p>
        <h4> 稻壳网——是什么？和 DocCms X 的关系？ </h4>
        <p> <a target="_blank" href="http://www.doooc.com">Doooc.com —— [ 中文名：稻壳网</a> ] 基于 DocCms X 的多用户网站托管、推广平台，致力于为 DocCms 粉丝打造一款高效、便捷、专业 且具有分享精神的一站式信息化全案推广营销平台。使企业品牌信息化建设、推广工作变得不再复杂，并可与 DocCms X 单用户版本实现数据互用互通，为广大粉丝创造出一款与众不同的特色Cms产品，并为广大用户创造出最大的商业价值 。 </p>
      </div>
    </div>
  </div>
  <div class="clr"></div>
</div>
<div class="clear"></div>
<div class="admin_info">
  <p><span>服务器IP</span><?php echo gethostbyname($_SERVER["SERVER_NAME"]);?></p>
  <p><span>站点状态</span><?php echo WEBOPEN?'开启':'关闭'; ?></p>
  <p><span>DocCMS版本</span><?php echo VERSION ?></p>
  <div class="spaceuse"> <span>空间统计</span>
    <div id="space">
      <div id="using"></div>
    </div>
    <span style="padding-left:12px;" id="num"> 计算中...</span> <span>
    <input type="button" value="重新计算" onclick="recounts()" id="countbut" />
    </span></div>
  <div class="clr"></div>
</div>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
	$.ajax({
		type:"POST",
		url:"index.php?a=readDirSize",
		timeout:"100000",
		async  :true,
		cache:false,                                
		success: function(html){
			if(parseInt(html)<=<?php echo WEBSIZE?>)
				$("#using").animate({width:(parseInt(html)/<?php echo WEBSIZE?WEBSIZE:'500'?>*100).toFixed(2)+"%"},1000);
			else
				$("#using").css({width:"100%",background:"#F00"});
			$("#num").empty();
			$("#num").append(parseInt(html)+"MB/<a class='spacecount' href='./index.php?m=system&s=options' title='点击修改空间总容量'><?php echo WEBSIZE?WEBSIZE:'500'?> MB</a> ("+(parseInt(html)/<?php echo WEBSIZE?WEBSIZE:'500'?>*100).toFixed(2)+"%)");
		},
		error:function(){	
		}
	});
})
function recounts()
{
	document.getElementById("countbut").disabled=true;
	$("#using").css({width:"100%"});	
	$("#num").empty();
	$("#num").append('计算中...');
	document.getElementById("countbut").value="请稍后...";
	$.ajax({
		type:"POST",
		url:"index.php?a=readDirSize&type=retry",
		timeout:"100000",
		async  :true,
		cache:false,                                
		success: function(html){	
			if(parseInt(html)<=<?php echo WEBSIZE?>)
				$("#using").animate({width:(parseInt(html)/<?php echo WEBSIZE?WEBSIZE:'500'?>*100).toFixed(2)+"%"},1000);
			else
				$("#using").css({width:"100%",background:"#F00"});
			$("#num").empty();
			$("#num").append(parseInt(html)+"MB/<a class='spacecount' href='./index.php?m=system&s=options' title='点击修改空间总容量'><?php echo WEBSIZE?WEBSIZE:'500'?> MB</a> ("+(parseInt(html)/<?php echo WEBSIZE?WEBSIZE:'500'?>*100).toFixed(2)+"%)");
			document.getElementById("countbut").disabled=false;
			document.getElementById("countbut").value="重新计算";
		},
		error:function(){	
		}
	});
}
</script> 