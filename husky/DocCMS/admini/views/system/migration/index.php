<link rel="stylesheet" href="./views/system/migration/css/migration.css" />

<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="./index.php?m=system&s=migration">网站迁移</a></div>
<div id="tabs1">
	<div class="menu1box">
	   <ul id="menu1">
	    <li class="hover" onclick="setTab(1,0)"><a href="#">数据库</a></li>
	    <li onclick="setTab(1,1)"><a href="#">备份</a></li>
		<li><a href="http://faq.doooc.com/rumen#" target="_blank">新手帮助</a></li>
	   </ul>
	</div>
	<div class="main1box">
	   <div class="main" id="main1">
		    <ul class="block"><li><?php require dirname(__FILE__).'/backupManage/database.php';?></li></ul>
		    <ul><li><?php require dirname(__FILE__).'/backupManage/backup.php';?></li></ul>
            <ul></ul>
	   </div>
	</div>
</div>
<script src="./views/system/migration/js/backup.js"></script>