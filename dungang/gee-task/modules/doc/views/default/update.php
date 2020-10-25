<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\doc\models\Doc */

$this->title = 'Update Doc: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal"
		aria-hidden="true">&times;</button>
		<h4 class="modal-title"><?= Html::encode($this->title) ?></h4>
</div>
<div class="modal-body">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
