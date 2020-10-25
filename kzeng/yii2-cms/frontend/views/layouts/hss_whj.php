<?php
use yii\helpers\Url;
use yii\helpers\Html;
use frontend\assets\HssWhjAsset;
use yii\bootstrap\Nav;
use yeesoft\models\Menu;

Yii::$app->assetManager->forceCopy = true;
HssWhjAsset::register($this);
?>
<?php $this->beginPage() ?>
<!doctype html>
<html>
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <!-- <title>黄石市文化新闻出版广电局-首页</title> -->
    <?php $this->head() ?>
    <style type="text/css">
        #hold_left {
            position: fixed;
            left: 0;
            top: 37%;
            width: 100px;
            height: 100px;
            background: blue;
        }
        .footer-rel{
          position: relative;
        }
        #hold_bottom {
            position: fixed;
            left: 0;
            top: 75%;
            width: 100px;
            height: 100px;
            background: blue;
        }
    </style>
</head>
<?php $this->beginBody() ?>
<body>
<!------头部------>
<div class="head">
  <div class="logo"> <img src="<?= Url::to('@web/hss_whj/images/logo.png') ?>"> </div>
  <div class="menu">
    <div class="nav">
      <ul>
        <li><a class="hover" href="<?= Url::to('site/index', true) ?>">首页</a></li>
        <li><a href="<?= Url::to('categories/whhs', true) ?>">文化黄石</a>
          <ul>
            <li><a href="<?= Url::to('category/shwh', true) ?>">社会文化</a></li>
            <li><a href="<?= Url::to('category/whsc', true) ?>">文化市场</a></li>
            <li><a href="<?= Url::to('category/wwbw', true) ?>">文物博物</a></li>
            <li><a href="<?= Url::to('category/xwcb', true) ?>">新闻出版</a></li>
          </ul>
        </li>
        <li><a href="<?= Url::to('categories/ddjs', true) ?>">党的建设</a>
            <ul>
              <li><a href="<?= Url::to('category/zdjs', true) ?>">制度建设</a></li>
              <li><a href="<?= Url::to('category/djdt', true) ?>">党建动态</a></li>
            </ul>
        </li>
        <li><a href="<?= Url::to('category/zwgk', true) ?>">政务公开</a></li>
        <li><a href="<?= Url::to('category/zcfg', true) ?>">政策法规</a></li>
        <li><a href="<?= Url::to('category/bszn', true) ?>">网上办事</a></li>
      </ul>
    </div>
  </div>
</div>
<!----头部 end---->
<?= $content ?><!-- 主题内容 -->
<!------foot-------->
<div class="footbg footer-rel">
  <div id='hold_bottom' style="width:153px;height:80px;"><img src="<?= Url::to('@web/hss_whj/images/jw.png') ?>" alt="" width='153' height='80'><img class="t-close close-2" src="<?= Url::to('@web/hss_whj/images/t-close.png') ?>" alt=""></div>
  <div class="foot-top" style="padding-top: 0;"> <!-- <a href="">信用中国</a>| <a href="">信用湖北</a>| <a href="">信用黄石</a> --> </div>
  <div class="foot-bot"> 黄石市文化新闻出版广电局 版权所有<br>
    黄石市安达路6号 邮编：435003 电话：0714-6359550<br>
    鄂ICP备11018575号  邮箱： hswhjbgs@sina.com </div>
  <div class="foot-img"> <a href="http://bszs.conac.cn/sitename?method=show&id=08E616E2D4F5109FE053022819AC8824"><img src="<?= Url::to('@web/hss_whj/images/dzjg.png') ?>"></a>
  <a href="http://121.43.68.40/exposure/jiucuo.html?site_code=4202000045&url=http:%3A%2F%2Fwww.huangshi.gov.cn"><img src="<?= Url::to('@web/hss_whj/images/error.png') ?>"></a> </div>
</div>
<div id='hold_left' style="height:233px;width:153px;"><img onclick="window.location.href = <?= Url::to('@web/hss_whj/images/hxjzg.jpg') ?>" src="<?= Url::to('@web/hss_whj/images/hxjzg.jpg') ?>" alt="" width='153' height='233'><img class="t-close close-1" src="<?= Url::to('@web/hss_whj/images/t-close.png') ?>" alt=""></div>
<!------foot end-------->
<?php $this->endBody() ?>

<script type="text/javascript">
    $(function() {
		//幻灯片
        new slide("#main-slide", "cur", "100%", "100%", 1);
        $('.close-1').click(function(){
            $('#hold_left').hide();
        });
        $('.close-2').click(function(){
            $('#hold_bottom').hide();
        });
    });

	//选项卡
	function setTab(name,cursel,n){
    	for(i=1;i<=n;i++){
        	var menu=document.getElementById(name+i);/* two1 */
        	var con=document.getElementById("con_"+name+"_"+i);/* con_two_1 */
        	menu.className=i==cursel?"hover":"";/*三目运算  等号优先*/
        	con.style.display=i==cursel?"block":"none";
    	}
	}
</script>
</body>
</html>
<?php $this->endPage() ?>
