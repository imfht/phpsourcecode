<title><?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $getGamesName[0];?>_918游戏平台</title>
 <meta name="description" content="918游戏平台-<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $getGamesName[0];?>给你最想玩的游戏" />
  <meta name="keywords" content="918游戏平台,<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $getGamesName[0];?>,918游戏平台-<?php $getGamesName=Games::model()->getGamesName($_GET['id']); echo  $getGamesName[0];?>" />
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
<div id="container" >
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery.valiLogin.js"></script>
<?php include("_login.php");?>

<div id="main">
  <div id="main_left">
    <div id="main_left_01">
      <h1><span>热点活动</span><?php echo CHtml::link('更多>>',array('list','tid'=>1,'id'=>$_GET['id']));?></h1>
      <div  class="mei">
        <ul>
        <?php $i=0; foreach ($model as $m):?>
        <?php if ($m->tid==1&&$i<4):?>
          <li><img src="<?php echo Yii::app()->baseUrl;?>/images/list.png" width="6" height="10" style="padding-right:6px;" />
          	<?php echo CHtml::link($m->tilte,array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?>
            <p><span><?php echo CHtml::link(date('Y-m-d',$m->create_time),array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?></span> 
            <span style="padding-left:20px;"><?php echo CHtml::link(date('H:i',$m->create_time),array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?></span>
            </p>
          </li>
          <?php $i++;?>
          <?php endif;?>
         <?php endforeach;?>
        </ul>
      </div>
    </div>
    <div id="main_left_02">
      <h1><span>活动公告</span><?php echo CHtml::link('更多>>',array('list','tid'=>2,'id'=>$_GET['id']));?></h1>
      <div  class="mei">
        <ul>
          <?php $i=0; foreach ($model as $m):?>
        <?php if ($m->tid==2 && $i<4):?>
          <li><img src="<?php echo Yii::app()->baseUrl;?>/images/list.png" width="6" height="10" style="padding-right:6px;" />
          	<?php echo CHtml::link($m->tilte,array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?>
            <p><span><?php echo CHtml::link(date('Y-m-d',$m->create_time),array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?></span> 
            <span style="padding-left:20px;"><?php echo CHtml::link(date('H:i',$m->create_time),array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?></span>
            </p>
          </li>
          <?php $i++;?>
          <?php endif;?>
         <?php endforeach;?>
        </ul>
      </div>
    </div>
    
    <div id="main_left_04">
      <h1><span>游戏攻略</span><?php echo CHtml::link('更多>>',array('list','tid'=>4,'id'=>$_GET['id']));?></h1>
      <div  class="mei">
        <ul>
          <?php $i=0; foreach ($model as $m):?>
          <?php if ($m->tid==4 && $i<4):?>
          <li><img src="<?php echo Yii::app()->baseUrl;?>/images/list.png" width="6" height="10" style="padding-right:6px;" />
          	<?php echo CHtml::link($m->tilte,array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?>
            <p><span><?php echo CHtml::link(date('Y-m-d',$m->create_time),array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?></span> 
            <span style="padding-left:20px;"><?php echo CHtml::link(date('H:i',$m->create_time),array('view','id'=>$m->gid,'gid'=>$m->id,'tid'=>$m->tid));?></span>
            </p>
          </li>
          <?php $i++;?>
          <?php endif;?>
         <?php endforeach;?>
        </ul>
      </div>
    </div>
    
  </div>
  <div id="main_right">
    <div id="main_right_01">
      <h1 style="width:280px; background:url(<?php echo Yii::app()->baseUrl;?>/images/right_bj.jpg) no-repeat;"><span>游戏截图</span></h1>
      <ul>
        <li><a href="#"><img src="<?php echo Yii::app()->baseUrl;?>/images/game_03.jpg" width="212" height="129" /></a></li>
        <li><a href="#"><img src="<?php echo Yii::app()->baseUrl;?>/images/game_03.jpg" width="212" height="129"  /></a></li>
        <li><a href="#"><img src="<?php echo Yii::app()->baseUrl;?>/images/game_03.jpg" width="212" height="129" /></a></li>
        <li><a href="#"><img src="<?php echo Yii::app()->baseUrl;?>/images/game_03.jpg" width="212" height="129"  /></a></li>
        <li style="padding-bottom:30px;"><a href="#"><img src="<?php echo Yii::app()->baseUrl;?>/images/game_03.jpg" width="212" height="129"  /></a></li>
      </ul>
    </div>
  </div>
</div>
</div>