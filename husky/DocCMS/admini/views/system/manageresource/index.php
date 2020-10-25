<style>
a{text-decoration:none;}
#mainNav{margin:10px 0;}
.placeholder{width:50px;}

/*popup*/
#createUI{display:none;}
.mesWindow{background:#fff; width:auto; padding-bottom:10px;}
.mesWindowTop{ width:auto; height:24px; line-height:20px; padding:6px 10px 0 17px; background:url(images/maptop.png) repeat-x; font-weight:bold;}
.mesWindowTop span{ float:left;}
.mesWindowTop .close{background:url(images/cls.gif);border:none;cursor:pointer;width:20px; height:20px;float:right;}
.mesWindowTop .close:hover{ background:url(images/cls.gif) 0 -20px no-repeat;}
.mesWindowContent{height:auto;}
.mesWindowContent ul{margin:12px;}
.mesWindowContent ul li{ line-height:28px;clear:both;}
.mesWindowContent ul li span{ width:60px; display:block; float:left;}
.mesWindowContent ul li input{ width:500px; }

#button,#close{width:80px; float:right;margin:0 8px; }
#tit,#wit{display:none;}
#dirs{ width:500px;margin-left:60px;border:1px #ccc solid;}
</style>
<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="?m=system&s=manageresource">资源管理</a></div>
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="70%">
	  <h3>资源管理器</h3>
	  <a href="./index.php?m=system&s=manageresource&path=<?php echo $data['path'];?>" class="creatbt" title="刷新"><img src="images/s_manageresource/icon_refresh.png"/></a>
	  <a href="javascript:;" class="creatbt" id="create" title="创建目录"><img src="images/s_manageresource/icon_folder_create.png"/ path="<?php echo $data['path']?>" ></a>
	  <a href="./index.php?m=system&s=manageresource&path=<?php echo $data['parent'];?>" class="creatbt" title="返回上级目录"><img src="images/s_manageresource/icon_pre.png"/></a>
	  <a href="javascript:;" class="creatbt" id="update" title="上传文件到当前目录"><img src="images/s_manageresource/icon_upload.png"/ path="<?php echo $data['path']?>" ></span>
	  <a href="./index.php?m=system&s=manageresource&a=cleanCache&path=<?php echo $data['path'];?>" class="creatbt" title="清理当前目录" onclick="return confirm('您确认要清理当前目录?一旦清理，将不可恢复！');"><img src="images/s_manageresource/icon_close.png"/></a>
	  <a href="./index.php?m=system&s=manageresource&a=buildSystemDir" class="creatbt">重建默认目录</a>
	  </td>
	  <td width="30%"><span id="static">统计：<?php echo $data['static']; ?></span></td>
	</tr>
</table>


<ul id="tree">
<?php foreach($data['d'] as $k=>$v){ ?>
	<li class="dir">
		<span class="title">
			<a href="./index.php?m=system&s=manageresource&path=<?php echo $data['path'].$v;?>/" title="下一级">
				<img src="images/s_manageresource/icon_folder.png"/><?php echo $v;?>
			</a>
		</span>
		<span class="fsize">&nbsp;</span>
		<span class="created"><?php echo date('Y-m-d',fileatime ($k));?></span>
		<span class="next">
			<a href="./index.php?m=system&s=manageresource&path=<?php echo $data['path'].$v;?>/" title="下一级">
				<img src="images/s_manageresource/icon_next.png"/>
			</a>
		</span>
		<span class="placeholder">&nbsp;</span>
		<span class="del">
			<a href="./index.php?m=system&s=manageresource&a=cleanCache&path=<?php echo $data['path'].$v;?>/" title="删除">
				<img src="images/s_manageresource/icon_close.png"/>
			</a>
		</span>
	</li>
<?php 
}
foreach($data['f'] as $k=>$v){ 	
?>
	<li class="file">
		<span class="title">
			<a href="javascript:;" target="_blank">
				<img src="images/s_manageresource/icon_text.png" alt="文件"/><?php echo $v;?>
			</a>
		</span>
		<span class="fsize"><?php echo DisplayFileSize(filesize($k));?></span>
		<span class="created"><?php echo date('Y-m-d',fileatime ($k));?></span>
		<span class="viewfile">
			<a href="./index.php?m=system&s=manageresource&a=viewfile&path=<?php echo $data['path'];?>&filename=<?php echo $v;?>" title="查看当前文件" target="_blank">
				<img src="images/s_manageresource/icon_view.jpg"/>
			</a>
		</span>
		<span class="download">
			<a href="./index.php?m=system&s=manageresource&a=download&path=<?php echo $data['path'];?>&filename=<?php echo $v;?>" title="下载当前文件">
				<img src="images/s_manageresource/icon_download.png"/>
			</a>
		</span>
		<span class="del">
			<a href="./index.php?m=system&s=manageresource&a=cleanCache&path=<?php echo $data['path'].$v;?>" title="删除当前文件">
				<img src="images/s_manageresource/icon_close.png"/>
			</a>
		</span>
		
	</li>
<?php 
}
if(empty($data['d']) && empty($data['f'])){
	echo '<li class="dir"><img border="0" src="./images/light.gif">友情提示：</li><li class="dir">{ 暂无可管理的资源文件和目录!  }</li><li class="dir">{ 在这里，您可以通过在线管理您的网站 upload 目录的资源文件，可替代您每次登录、退出、浏览等繁琐的ftp功能。 }</li><li class="dir">{ 请注意：此目录为网站资源文件，为网站系统重要目录，不可随意更改目录名称，删除文件时也请谨慎操作，做好数据备份，否则一旦删除将无法恢复！ }</li>';
}
?>
</ul>
<div id="createUI" style="display:block;"></div>
<!--  <div id="page">分页</div> -->
<script src="../inc/js/jquery.form.js"></script>
<script src="../inc/js/s_manageresource/popup.js"></script>
<script src="../inc/js/s_manageresource/manageresource.js"></script>
