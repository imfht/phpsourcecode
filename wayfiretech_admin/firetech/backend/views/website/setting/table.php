<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-29 00:18:02
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-29 00:18:02
 */


use yii\helpers\Html;
use yii\widgets\ActiveForm;

$acname = Yii::$app->controller->action->id;

/* @var $model \app\models\forms\ConfigurationForm */
/* @var $this \yii\web\View */
?>
<ul class="nav nav-tabs">
    <li <?php if ($acname == 'website') : ?> class="active" <?php endif; ?>>
        <?= Html::a('站点设置', ['website', 'plugins' => $plugins], []) ?>
    </li>
</ul>