<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <link rel="stylesheet" href="/css/jquery.fullPage.css">
    <script src="/js/jquery.fullPage.min.js"></script>


    <style>
        body .btn{
            font-family:"Microsoft Yahei";
        }
        .section1 { background: url(/uploads/1.png) 50%;}
        /*.section2 { background: url(http://idowebok.u.qiniudn.com/77/2.jpg) 50%;}*/
/*        .section3 { background: url(https://git.oschina.net/uploads/images/2017/0627/150056_dcccca71_537766.jpeg) 50%;}
        .section4 { background: url(https://git.oschina.net/uploads/images/2017/0627/150320_043a3b19_537766.jpeg) 50%;}*/

            .navbar-inverse {
                background-color: rgba(255, 255, 255, .15);
                border-color: #337ab7;
            }

            .navbar-inverse .navbar-nav > .active > a, .navbar-inverse .navbar-nav > .active > a:hover, .navbar-inverse .navbar-nav > .active > a:focus {
                color: #fff;
                background-color: #337ab7;
            }

            .navbar-inverse .navbar-toggle .icon-bar {
                background-color: #c6e0f7;
            }

            .navbar-inverse .navbar-toggle {
                border-color: #c6e0f7;
            }

            .navbar-inverse .btn-link {
                color: #333;
            }

            .navbar-inverse .navbar-brand {
                color: #333;
            }

            .btn-success {
            color: #f5f5f5;
            background-color: rgba(255, 255, 255, .15);
            border-color: #f5f5f5;
            }
    </style>


</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '超级表单',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        ['label' => '首页', 'url' => ['/site/index']],
        // ['label' => 'About', 'url' => ['/site/about']],
        // ['label' => 'Contact', 'url' => ['/site/contact']],
    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '注册', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton(
                '退出 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link']
            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>
     <?= $content ?>
</div>




<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
