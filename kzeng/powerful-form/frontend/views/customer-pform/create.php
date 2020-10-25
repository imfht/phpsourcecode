<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\CustomerPform */

$pform_uid = $_GET["pform_uid"];
$pform = backend\models\Pform::find()->where(['uid' => $pform_uid])->one();

$this->title = $pform->title;
// $this->params['breadcrumbs'][] = ['label' => 'Customer Pforms', 'url' => ['index']];
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="customer-pform-create">

    <?= $this->render('_form1', [
        'pform' => $pform,
    ]) ?>

</div>
