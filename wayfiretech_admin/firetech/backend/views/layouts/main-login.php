<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-22 17:32:02
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-06-16 18:11:59
 */


use backend\assets\AppAsset;
use common\widgets\adminlte\AdminLteAsset;
use common\widgets\adminlte\Alert;
use yii\helpers\Html;
use yii\web\View;

/* @var $this \yii\web\View */
/* @var $content string */

$bundle = common\widgets\adminlte\AdminLteAsset::register($this);
$bundle->js[] = 'dist/js/canva_moving_effect.js'; // dynamic file added
$this->registerJs("window.sysinfo={
    'CSRF_HEADER':'". \yii\web\Request::CSRF_HEADER ."',".
    "csrfToken:'". Yii::$app->request->csrfToken."'};",View::POS_HEAD);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style lang="">
        body {
            overflow-y: hidden;
        }

        /*-- bg effect --*/

        #bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }

        #bg canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        /*-- //bg effect --*/

        /*--responsive--*/

        @media(max-width:1920px) {
            h1 {
                font-size: 3.5vw;
            }
        }

        @media(max-width:1024px) {
            h1 {
                font-size: 4.5vw;
            }
        }

        @media(max-width:800px) {
            h1 {
                font-size: 2.6em;
            }
        }

        @media(max-width:480px) {
            h1 {
                font-size: 2.3em;
                letter-spacing: 1px;
            }

            .sub-main-w3 form {
                padding: 7.5vw;
            }

            .footer p {
                letter-spacing: 1px;
            }
        }

        @media(max-width:414px) {

            .form-style-agile input[type="text"],
            .form-style-agile input[type="password"] {
                font-size: 13px;
                padding: 13px 15px;
            }

            .wthree-text ul li:nth-child(1),
            .wthree-text ul li:nth-child(2) {
                float: none;
            }

            .wthree-text ul li:nth-child(2) {
                margin-top: 10px;
            }

            .sub-main-w3 input[type="submit"] {
                width: 56%;
            }

            .wthree-text ul li {
                display: block;
            }
        }

        @media(max-width:320px) {
            h1 {
                font-size: 1.8em;
                margin: 5vw 1vw;
            }

            .sub-main-w3 form {
                padding: 25px 14px;
            }
        }

        /*--//responsive--*/
    </style>
    <script>
        addEventListener("load", function() {
            setTimeout(hideURLbar, 0);
        }, false);

        function hideURLbar() {
            window.scrollTo(0, 1);
        }
    </script>
</head>

<body class="login-page">
    <div id="bg">
        <canvas></canvas>
        <canvas></canvas>
        <canvas></canvas>
    </div>
    <?php $this->beginBody() ?>
    <?= Alert::widget(); ?>

    <?= $content ?>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>