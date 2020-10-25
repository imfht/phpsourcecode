<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-31 17:11:53
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-31 17:35:16
 */
use yii\widgets\Menu;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

$asset = yii\gii\GiiAsset::register($this);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="none">
    <?php $this->registerCsrfMetaTags(); ?>
    <title><?= Html::encode($this->title); ?></title>
    <?php $this->head(); ?>
</head>
<body>
    <div class="page-container">
        <?php $this->beginBody(); ?>
     
        <div class="container content-container">
            <?= $content; ?>
        </div>
        <div class="footer-fix"></div>
    </div>
    <footer class="footer border-top">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <p>A Product of <a href="http://www.yiisoft.com/">Yii Software LLC</a></p>
                </div>
                <div class="col-6">
                    <p class="text-right"><?= Yii::powered(); ?></p>
                </div>
            </div>
        </div>
    </footer>
<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
