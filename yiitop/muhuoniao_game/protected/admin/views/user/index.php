<div id="side_right">
<h2><strong>用户管理</strong></h2>
<h3>用户列表</h3>
<table width="900" border="0" bordercolor="#999999">
<thead>
  <tr>
    <th>用户ID</th>
    <th>用户名</th>
    <th>用户权限</th>
    <th>创建时间</th>
    <th>登录时间</th>
    <th>IP</th>
  </tr>
  </thead>
  <tbody>
 <?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
	'template'=>'{items}',
)); ?>


</tbody>

</table>
<!--<p class="pagination"><span class="page_left">每页显示记录<?php echo $pages->pageSize;?>条，共<?php echo $pages->pageCount;?>页<?php echo $pages->itemCount;?>条记录</span></p>-->
<?php $this->widget('CLinkPager',array(
	'pages'=>$pages,
	'header'=>'',
	'cssFile'=>false,
	'footer'=>''
));?>
</div>
