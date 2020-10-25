<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-10 01:17:29
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 01:17:32
 */
 
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/setpassword', 'token' => $user->password_reset_token]);
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Follow the link below to reset your password:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
