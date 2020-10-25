<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<HTML>
 <HEAD>
     <TITLE><?php echo getSiteOption('siteName'); ?> 后台管理</TITLE>
  <meta http-equiv="content-type" content="text/html; charset=UTF-8">
  <link rel="stylesheet" href="/Public/zTree/css/zTreeStyle/zTreeStyle.css" type="text/css">
  <link rel="apple-touch-icon" href="/Public/appicon.png">
  <link rel="shortcut icon" href="/Public/appicon.png">
  <style>
	body {
	background-color: white;
	margin:0; padding:0;
	text-align: center;
        
	}
	div, p, table, th, td {
		list-style:none;
		margin:0; padding:0;
		color:#333; font-size:12px;
		font-family:dotum, Verdana, Arial, Helvetica, AppleGothic, sans-serif;
	}
	#testIframe {margin-left: 10px;}
  </style>
<script type="text/javascript" src="/Public/js/jquery.min.js"></script>
<script type="text/javascript" src="/Public/zTree/js/jquery.ztree.core-3.5.min.js"></script>
  <SCRIPT type="text/javascript" >
  <!--
	var zTree;
	var demoIframe;

	var setting = {
		view: {
			dblClickExpand: false,
			showLine: true,
			selectedMulti: false
		},
		data: {
			simpleData: {
				enable:true,
				idKey: "id",
				pIdKey: "pId",
				rootPId: ""
			}
		},
		callback: {
			beforeClick: function(treeId, treeNode) {
				var zTree = $.fn.zTree.getZTreeObj("tree");
				if (treeNode.isParent) {
					zTree.expandNode(treeNode);
					return false;
				} else {
					demoIframe.attr("src",treeNode.file + ".html");
					return true;
				}
			}
		}
	};

	var zNodes =[
{id:1, pId:0, name:"文章管理", open:true},
{id:101, pId:1, name:"撰写文章", url:"<?php echo U('Admin/Admin/addContent'); ?>", target:"main"},
{id:102, pId:1, name:"所有文章",  url:"<?php echo U('Admin/Admin/listContent'); ?>", target:"main"},
{id:103, pId:1, name:"添加分类",  url:"<?php echo U('Admin/Admin/addCategory'); ?>", target:"main"},
{id:104, pId:1, name:"所有分类",  url:"<?php echo U('Admin/Admin/listCategory'); ?>", target:"main"},
{id:105, pId:1, name:"草稿箱",  url:"<?php echo U('Admin/Admin/listDraft'); ?>", target:"main"},
{id:106, pId:1, name:"回收站",  url:"<?php echo U('Admin/Admin/listCallback'); ?>", target:"main"},

{id:2, pId:0, name:"多媒体管理", open:false},
{id:201, pId:2, name:"媒体库", url:"<?php echo U('Admin/Media/index'); ?>", target:"main"},
{id:202, pId:2, name:"添加媒体",  url:"<?php echo U('Admin/Media/add'); ?>", target:"main"},

{id:3, pId:0, name:"链接管理", open:false},
{id:301, pId:3, name:"添加链接", url:"<?php echo U('Admin/Links/addLinks'); ?>", target:"main"},
{id:302, pId:3, name:"查看链接",  url:"<?php echo U('Admin/Links/listLinks'); ?>", target:"main"},

{id:4, pId:0, name:"权限管理", open:false},
{id:401, pId:4, name:"用户列表", url:"<?php echo U('Admin/Rbac/index'); ?>", target:"main"},
{id:402, pId:4, name:"角色列表",  url:"<?php echo U('Admin/Rbac/role'); ?>", target:"main"},
{id:403, pId:4, name:"节点列表",  url:"<?php echo U('Admin/Rbac/node'); ?>", target:"main"},
{id:404, pId:4, name:"添加用户",  url:"<?php echo U('Admin/Rbac/addUser'); ?>", target:"main"},
{id:405, pId:4, name:"添加角色",  url:"<?php echo U('Admin/Rbac/addRole'); ?>", target:"main"},

{id:5, pId:0, name:"在线编辑", open:false},
{id:501, pId:5, name:"首页模板",  url:"<?php echo U('Admin/Config/config',array('remark'=>'首页模板','filename'=>$indexView)); ?>", target:"main"},
{id:502, pId:5, name:"文章模板",  url:"<?php echo U('Admin/Config/config',array('remark'=>'文章模板','filename'=>$contentView)); ?>", target:"main"},
{id:503, pId:5, name:"分类模板",  url:"<?php echo U('Admin/Config/config',array('remark'=>'分类模板','filename'=>$categoryView)); ?>", target:"main"},
{id:504, pId:5, name:"头部文件",  url:"<?php echo U('Admin/Config/config',array('remark'=>'头部文件','filename'=>$headerView)); ?>", target:"main"},
{id:505, pId:5, name:"底部文件",  url:"<?php echo U('Admin/Config/config',array('remark'=>'底部模板','filename'=>$footerView)); ?>", target:"main"},
{id:506, pId:5, name:"配置文件",  url:"<?php echo U('Admin/Config/config',array('remark'=>'配置文件','filename'=>$conf)); ?>", target:"main"},

{id:6, pId:0, name:"SiteMap管理", open:false},
{id:601, pId:6, name:"更新HTML地图", url:"<?php echo U('Admin/SiteMap/createHtml',array('act'=>'r'),''); ?>", target:"main"},
{id:602, pId:6, name:"更新XML地图",  url:"<?php echo U('Admin/SiteMap/createXML',array('act'=>'r'),''); ?>", target:"main"},
{id:603, pId:6, name:"浏览HTML地图",  url:"<?php echo $app_path .'/sitemap.html'; ?>", target:"main"},
{id:604, pId:6, name:"浏览XML地图",  url:"<?php echo $app_path .'/sitemap.xml'; ?>", target:"main"},

{id:7, pId:0, name:"数据管理", open:false},
{id:701, pId:7, name:"SQL命令", url:"<?php echo U('Admin/Data/sql'); ?>", target:"main"},
{id:702, pId:7, name:"数据备份",  url:"<?php echo U('Admin/Data/backupDB'); ?>", target:"main"},
{id:703, pId:7, name:"修复优化",  url:"<?php echo U('Admin/Data/opimize'); ?>", target:"main"},

{id:8, pId:0, name:"图表统计", open:false},
{id:801, pId:8, name:"文章月份统计", url:"<?php echo U('Admin/Char/content'); ?>", target:"main"},
{id:802, pId:8, name:"文章分类统计",  url:"<?php echo U('Admin/Char/category'); ?>", target:"main"},

{id:10, pId:0, name:"个人设置", open:false},
{id:1001, pId:10, name:"修改密码", url:"<?php echo U('Admin/Self/updatePwd'); ?>", target:"main"},

{id:87, pId:0, name:"清理缓存",  url:"<?php echo U('Admin/Freshen/freshen'); ?>", target:"main"},
{id:88, pId:0, name:"站点信息",  url:"<?php echo U('Admin/Siteoption/index'); ?>", target:"main"},
{id:89, pId:0, name:"关于",  url:"<?php echo U('Admin/Admin/help'); ?>", target:"main"},
{id:90, pId:0, name:"系统信息",  url:"<?php echo U('Admin/Admin/main'); ?>", target:"main"},
{id:91, pId:0, name:"刷新页面", url:"javascript:parent.location.reload()", target:"_parent"},
{id:92, pId:0, name:"安全退出", url:"<?php echo U('Admin/Admin/sale'); ?>", target:"_parent"},
{id:93, pId:0, name:"移动版", url:"<?php echo U('Admin/Admin/mobile'); ?>", target:"_parent"},



];

 

	$(document).ready(function(){
		var t = $("#tree");
		t = $.fn.zTree.init(t, setting, zNodes);
		demoIframe = $("#testIframe");
		demoIframe.bind("load", loadReady);
		var zTree = $.fn.zTree.getZTreeObj("tree");
		zTree.selectNode(zTree.getNodeByParam("id", 101));

	});

	function loadReady() {
		var bodyH = demoIframe.contents().find("body").get(0).scrollHeight,
		htmlH = demoIframe.contents().find("html").get(0).scrollHeight,
		maxH = Math.max(bodyH, htmlH), minH = Math.min(bodyH, htmlH),
		h = demoIframe.height() >= maxH ? minH:maxH ;
		if (h < 530) h = 530;
		demoIframe.height(h);
	}

  //-->
  </SCRIPT>
 </HEAD>

<BODY>
<TABLE border=0  align=left>
	<TR>
		<TD width=260px align=left valign=top style="BORDER-RIGHT: #999999 1px dashed">
			<ul id="tree" class="ztree" style="width:260px; overflow:auto;"></ul>
		</TD>
		<TD width=770px align=left valign=top>
        <IFRAME ID="testIframe" Name="main" FRAMEBORDER="0" SCROLLING="AUTO" width="100%"   SRC="<?php echo U('Admin/Admin/help');?>"></IFRAME></TD>
	</TR>
</TABLE>

</BODY>
</HTML>