<div id="side_right">
<h2><strong>用户管理</strong></h2>
<h3>用户列表</h3>
<table width="900" border="0" bordercolor="#999999">
<thead>
  <tr>
    <th>用户ID</th>
    <th>昵称</th>
    <th>最近登录时间</th>
    <th>身份证号</th>
    <th>邮箱</th>
    <th>QQ</th>
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
<?php $this->widget('CLinkPager',array(
	'pages'=>$pages,
	'header'=>'',
	'cssFile'=>false,
	'footer'=>''
));?>
</div>
