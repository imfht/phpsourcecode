<?php
function out_menu($param,$arr){
$sec_li='';
foreach($arr as $v){
$sec_li.='<li><a href="./detail_product.php?id='.$v['id'].'">'.$v['name'].'</a></li>';
}
$li1=$li2=$li3=$li4=$li5=$li6=$li7='';
if($param=='1') $li1=' class="current"';
if($param=='2') $li2=' class="current"';
if($param=='3') $li3=' class="current"';
if($param=='4') $li4=' class="current"';
if($param=='5') $li5=' class="current"';
if($param=='6') $li6=' class="current"';
if($param=='7') $li7=' class="current"';
echo '
<nav id="menu">
	<ul class="sf-menu">
		<li '.$li1.'><a HREF="./dashboard.php">控制面板</a></li>
		<li '.$li2.'><a HREF="./page.php">页面管理</a></li>	
		<li '.$li3.'>
			<a HREF="./product.php">产品管理</a>
			<ul>
				<li>
					<a HREF="./banner.php">广告显示</a>
				</li>
				<li>
					<a href="javascript:;">产品分类</a>
					<ul>'.
						$sec_li.
					'</ul>
				</li>
			</ul>
		</li>
		<li '.$li4.'><a HREF="./leave.php">留言管理</a></li>
		<li '.$li5.'><a HREF="./mix.php">杂项设置</a></li>
		<li '.$li6.'><a HREF="./mix_sidebar.php">侧边栏设置</a></li>
		<li '.$li7.'><a HREF="./backupSQL.php">数据库备份</a></li>
	</ul>
</nav>';
}