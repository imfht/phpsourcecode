<?php
	/**
	 *
	 * @var yii\web\View $this
	 * @var yeesoft\widgets\ActiveForm $form
	 * @var yeesoft\models\Role $model
	 */

	use yii\helpers\Html;

	$this->title = '新建菜单';
	$this->params['breadcrumbs'][] = ['label' => '菜单管理', 'url' => ['/mp/mp-menu/index']];
	$this->params['breadcrumbs'][] = $this->title;
?>

<div class="menu-create">
    <h3 class="lte-hide-title"><?= Html::encode($this->title) ?></h3>
    <?= $this->render('_form', compact('model')) ?>
</div>