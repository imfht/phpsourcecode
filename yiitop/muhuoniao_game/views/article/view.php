<title><?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $model->tilte."_".ArticleType::model()->getArticleTypeName($model->tid)."_".$getGamesName[0]; ?>_918游戏平台</title>
 <meta name="description" content="918游戏平台<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $getGamesName[0].ArticleType::model()->getArticleTypeName($model->tid).$model->tilte.$model->description; ?>" />
  <meta name="keywords" content="<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $model->keywords.",".$model->tilte.",".ArticleType::model()->getArticleTypeName($model->tid).",".$getGamesName[0]; ?>,918游戏平台" />
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
<div id="meirong">
<!--      <h1><a href="../../list/<?php echo $model->gid ?>"><?php $getGamesName=Games::model()->getGamesName($model->gid);echo $getGamesName[0]?>首页</a>>><a href="../../list/<?php echo $model->gid ?>"><?php echo ArticleType::model()->getArticleTypeName($model->tid)?>-></a><b class="cl"><?php echo $model->tilte;?></b></h1>-->
      <h1>
      	<?php echo CHtml::link('918首页>>',array('site/index'));?>
      	<?php $getGamesName=Games::model()->getGamesName($model->gid); echo CHtml::link($getGamesName[0].'>>',array('article/index','id'=>$model->gid))?>
      	<?php echo CHtml::link(ArticleType::model()->getArticleTypeName($model->tid).'>>',array('article/list','id'=>$model->gid,'tid'=>$model->tid)); ?>
      	<div style="color:#FC8D03;"><?php echo $model->tilte;?></div>
      </h1>
      <style>
      	#meirong h1{
      		padding:0;
			margin:0;
			line-height:47px;
		}
		#meirong h1 a{
			padding:0 ;
			margin:0;
			float:left;
		}
      </style>
      <div  class="meirong_01">
       <h2><?php echo $model->tilte; ?><br /><span><?php echo date('Y-m-d',$model->create_time);?></span></h2>

       	<?php echo $model->content;?>

		
      
       
      </div>
    </div>
    
<div id="list_right">
     
     <div id="fenlei">
     <h1 style="background:url("<?php echo Yii::app()->baseUrl;?>/images/right_bj.jpg) no-repeat;">相关新闻</h1>
     <ul>
     	<?php 
     		$criteria = new CDbCriteria(array(
			'condition'=>'gid='.$model->gid,
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

