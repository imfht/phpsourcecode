<div id="side_right">
<h2><strong>栏目管理</strong></h2>
<h3>栏目列表</h3>
<table width="900" border="0" bordercolor="#999999">
<thead>
  <tr>
    <th>文章类型ID</th>
    <th>文章类型名称</th>
    <th>父级栏目</th>
    <th>创建作者</th>
    <th>更新人</th>
    <th>创建时间</th>
    <th>更新时间</th>
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
