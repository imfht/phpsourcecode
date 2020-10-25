<div id="side_right">
<h2><strong>订单管理</strong></h2>
<h3>订单列表</h3>
<table width="900" border="0" bordercolor="#999999">
<thead>
  <tr>
    <th>订单ID</th>
    <th>订单序号</th>
    <th>充值用户</th>
    <th>充值游戏</th>
    <th>充值大区</th>
    <th>充值金额</th>
    <th>Pay类型</th>
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