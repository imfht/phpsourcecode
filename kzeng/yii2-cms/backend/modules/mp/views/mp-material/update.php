<?php
	/**
	 * @var yeesoft\widgets\ActiveForm $form
	 * @var yeesoft\models\Role $model
	 */

	use yii\helpers\Html;

	$this->title = '更新菜单';
	$this->params['breadcrumbs'][] = '菜单管理', 'url' => ['/mp/mp-material/index']];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="material-update">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>