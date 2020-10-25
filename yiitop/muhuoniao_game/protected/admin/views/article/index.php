<div id="side_right">
<script type="text/javascript">
eval(function(p,a,c,k,e,r){e=function(c){return c.toString(36)};if('0'.replace(0,e)==0){while(c--)r[e(c)]=k[c];k=[function(e){return r[e]||e}];e=function(){return'[0-9a-df-q]'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}('$(3(){$(".6").click(3(e){4 $1=$(this).7(\'title\');if($1!=\'\'){4 $5="<5 8=\'0\' 9=\'200\' a=\'100\' />";$(\'b\').c($5);$("#0").7(\'1\',\'http://918s-upload.stor.sinaapp.com/\'+$1);$("#0").d({\'f\':\'g\',\'h\':e.i+j,\'k\':e.l+m,\'z-n\':o}).p()}else{4 $2="<2 8=\'0\' 9=\'200px\' a=\'100px\'><q>尚未添加缩略图</q></2>";$(\'b\').c($2);$("#0").d({\'f\':\'g\',\'h\':e.i+j,\'k\':e.l+m,\'z-n\':o}).p()}});$(".6").mouseout(3(){$("#0").remove()})})',[],27,'showImage|src|div|function|var|img|imgUrl|attr|id|width|height|body|append|css||position|absolute|top|pageY|10|left|pageX|20|index|9999|show|h1'.split('|'),0,{}))
</script>
<h2><strong>文章管理</strong></h2>
<h3>文章列表</h3>
<table width="900" border="0" bordercolor="#999999">
<thead>
  <tr>
    <th>文章ID</th>
    <th>文章标题</th>
    <th>游戏</th>
    <th>栏目</th>
    <th>关键字</th>
    <th>描述</th>
    <th>缩略图</th>
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
<!---------------side_right end---------------->

