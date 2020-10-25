<title><?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  ArticleType::model()->getArticleTypeName($_GET['tid'])."_".$getGamesName[0]; ?>_918游戏平台</title>
 <meta name="description" content="918平台游戏-<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $getGamesName[0].','.ArticleType::model()->getArticleTypeName($_GET['tid']).','.$getGamesName[0].ArticleType::model()->getArticleTypeName($_GET['tid']);?>给你最想玩的游戏" />
  <meta name="keywords" content="918游戏平台,<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $getGamesName[0].','.ArticleType::model()->getArticleTypeName($_GET['tid']).','.$getGamesName[0].ArticleType::model()->getArticleTypeName($_GET['tid']);?>," />
<link rel="stylesheet" href="<?php echo Yii::app()->baseUrl;?>/css/official.css" type="text/css" media="screen" />
<style>
<style>
*{
margin:0 auto;
padding:0;
}
body {
background:url(<?php echo Yii::app()->baseUrl;?>/images/big_bj.jpg) no-repeat scroll center 95px #f2f2f2;
font:12px Helvetica, Tahoma, Arial, sans-serif;
}





</style>
<script type="text/javascript">
var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F8cc15c51c6678071800fd36ffda82d58' type='text/javascript'%3E%3C/script%3E"));
</script>

</head>
<body>
<div id="header">
  <div id="main_header">
    <div id="nav"> <img src="<?php echo Yii::app()->baseUrl;?>/images/logo.jpg" style="float:left;" />
             <ul>
        <li style="padding-left:40px;"><a href="<?php echo Yii::app()->request->baseUrl; ?>/" class="home"><img src="<?php echo Yii::app()->baseUrl;?>/images/home.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/member/"><img src="<?php echo Yii::app()->baseUrl;?>/images/buluo.jpg" /></a></li>
        <li><a href="<?php echo Yii::app()->request->baseUrl; ?>/order/"><img src="<?php echo Yii::app()->baseUrl;?>/images/chongzhi.jpg" /></a></li>
 
      </ul>
    </div>
  </div>
</div>

<!--end-->
<div id="container">
<?php include("_login.php");?>

<div id="list">
<div id="list_left">
      <h1>
      	<?php echo CHtml::link('918首页>>',array('site/index'));?>
      	<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo CHtml::link($getGamesName[0].'>>',array('article/index','id'=>$_GET['id']))?>
      	<div style="color:#FC8D03;"><?php echo ArticleType::model()->getArticleTypeName($_GET['tid']); ?></div>
      </h1>
      <style>
      	#list_left h1{
      		padding-left:20px;
			margin:0;
			line-height:47px;
		}
		#list_left h1 a{
			padding:0 ;
			margin:0;
			float:left;
		}
      </style>
      <div  class="mei">
        <ul>
        
            <?php foreach ($models as $model):?>
            	<div><li><img src="<?php echo Yii::app()->baseUrl;?>/images/list.png" width="6" height="10" style="padding-right:6px;" />
            		<a href="../view/id/<?php echo $model->gid.'?gid='.$model->id;?>"><?php echo $model->tilte;?>
            			<p><span><?php echo date('Y-m-d',$model->create_time);?></span> <span style="padding-left:20px;"><?php echo date('H:i',$model->create_time);?></span></p>
           			</a>
           		</li></div>
            <?php endforeach;?>
			<div id="pager">	
			<?php
				$this->widget('CLinkPager',array(
				'header'=>'',
				'firstPageLabel' => '',
				'lastPageLabel' => '',
				'prevPageLabel' => '',
				'nextPageLabel' => '',
				'pages' => $pages,
				'maxButtonCount'=>8,
				'cssFile'=>false,
				)
				);
			?>
			</div>
        </ul>
      </div>
    </div>
    <script type="text/javascript">
    	$(function(){
			$("#list_left .mei ul div:odd").not("#pager").css("background","#ccc");
        })
    </script>
<style>

#pager{
	margin-top:50px;
	margin-left:370px;
}
#pager ul{
	backgrond:white;
}
#pager ul li{
	float:left;  
	padding:0;
	height:20px;
	line-height:20px;
	width:40px;
	text-align:center;
}
#pager .page{
	width:20px;
}
</style>
<div id="list_right">
     
     <div id="fenlei">
     <h1 style="background:url("<?php echo Yii::app()->baseUrl;?>/images/right_bj.jpg) no-repeat;">相关新闻</h1>
     <ul>
     	<?php 
     		$criteria = new CDbCriteria(array(
				'condition'=>' display=1 and gid='.$model->gid,
     			'limit'=>5,
				'order'=>'up_time DESC',//按照发表时间，进行倒排序
			));	
     		$dataProvider = new CActiveDataProvider('Article',array(
     			'pagination'=>false,
     			'criteria'=>$criteria,
     		));
     		$this->widget('zii.widgets.CListView', array(
			'dataProvider'=>$dataProvider,
			'itemView'=>'_view',
     		'summaryText'=>'',
			)); 
		?>
     </ul>
     </div>

    </div>
</div>
</div>