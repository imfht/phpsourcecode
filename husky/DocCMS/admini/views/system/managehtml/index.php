<div class="location">当前位置: <a href="./index.php">首 页</a> → <a href="./index.php?m=system&s=managechannel">操作员后台</a> → <a href="?m=system&s=managehtml">缓存管理</a></div>
<table width="100%" border="0" cellpadding="4" cellspacing="1" class="admintb">
    <tr class="adtbtitle">
      <td width="70%">
	  <h3>静态Html缓存管理器</h3>
      <a href="./index.php?m=system&s=managehtml&a=gohtml" title="一键生成所有页面静态缓存" class="creatbt">一键生成</a>
	  <a href="./index.php?m=system&s=managehtml&a=help" title="在线帮助" class="creatbt">在线帮助</a> 
      <a href="./index.php?m=system&s=managehtml&path=<?php echo $data['path'];?>" class="creatbt" title="刷新"><img src="./images/s_managehtml/icon_refresh.png"/></a>
	  <a href="./index.php?m=system&s=managehtml&path=<?php echo $data['parent'];?>" class="creatbt" title="返回上级目录"><img src="./images/s_managehtml/icon_up.png"/></a>
	  <a href="./index.php?m=system&s=managehtml&a=cleanCache&path=<?php echo $data['path'];?>" class="creatbt" title="一键清理当前缓存"  onclick="return confirm('您确认要清理当前缓存?一旦清理，将不可恢复！');"><img src="./images/s_managehtml/icon_close.png"/></a>
	  </td>
	  <td width="30%"><span id="static">统计：<?php echo empty($data['static'])?'0 文件':$data['static']; ?></span></td>
	</tr>
</table>
<ul id="tree">
<?php foreach($data['d'] as $k=>$v){ ?>
	<li class="dir">
		<span class="title">
			<a href="./index.php?m=system&s=managehtml&path=<?php echo $data['path'].$v;?>/" title="下一级">
				<img src="./images/s_managehtml/icon_folder.png"/><?php echo $v;?>
			</a>
		</span>
		<span class="created"><?php echo date('Y-m-d',fileatime ($k));?></span>
		<span class="del"><a href="./index.php?m=system&s=managehtml&a=cleanCache&path=<?php echo $data['path'].$v;?>/" title="删除">
		<img src="./images/s_managehtml/icon_close.png"/></a></span>
	</li>
<?php }
foreach($data['f'] as $k=>$v){ 
	if(strpos($request['path'],'dynamicHttp')!==false){
?>
	<li class="file">
		<span class="title">
			<a href="/<?php echo $tmp=base64_decode(substr($v,0,strlen($v)-5)); ?>" target="_blank">
				<img src="./images/s_managehtml/icon_text.png" alt="文件"/>/<?php echo $tmp;?>
			</a>
		</span>
		<span class="created"><?php echo date('Y-m-d',fileatime ($k));?></span>
		<span class="del">
		<a href="./index.php?m=system&s=managehtml&a=cleanCache&path=<?php echo $data['path'].$v;?>" title="清理缓存">
		<img src="./images/s_managehtml/icon_close.png"/></a>
		</span>
	</li>
<?php }else{ ?>
	<li class="file">
		<span class="title">
			<a href="<?php echo $data['path'].str_replace('index.html','',$v); ?>" target="_blank">
				<img src="./images/s_managehtml/icon_text.png" alt="文件"/><?php echo $v;?>
			</a>
		</span>
		<span class="created"><?php echo date('Y-m-d',fileatime ($k));?></span>
		<span class="del"><a href="./index.php?m=system&s=managehtml&a=cleanCache&path=<?php echo $data['path'].$v;?>" title="清理当前文件缓存"><img src="./images/s_managehtml/icon_close.png"/></a></span>
	</li>
<?php }
}?>
<?php

if(empty($data['d']) && empty($data['f']) && ($request['path']=='/' || $request['path']=='')){
	echo '<li class="dir"><img border="0" src="./images/light.gif">友情提示：</li><li class="dir">{ 目前暂无静态html缓存文件生成!  }</li><li class="dir">{ 您可以通过开启 “开始构建网站->站点设置->\'站点页面静态缓存生成设置\' 选项，将其设置为大于 0 天，然后通过网站前台链接访问页面时即会自动生成静态html缓存页面。” }</li><li class="dir">{ 当然，您也可以通过上面的 “一键生成” 按钮在上一条提示中开启缓存的基础上，直接手动方式主动将页面生成静态html }</li>';
}
else if(empty($data['d']) && empty($data['f'])){
	echo '<li class="dir"><img border="0" src="./images/light.gif">友情提示：</li><li class="dir">{ 当前目录暂无静态html缓存文件生成!  }</li>';
}
?>
</ul>
<!--  <div id="page">分页</div> -->