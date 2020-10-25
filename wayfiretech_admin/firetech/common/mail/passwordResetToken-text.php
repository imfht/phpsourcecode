<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-07-10 01:18:32
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-07-10 01:18:34
 */
 

/* @var $this yii\web\View */
/* @var $user common\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/setpassword', 'token' => $user->password_reset_token]);
?>
Hello <?= $user->username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
