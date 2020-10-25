<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
$this->context->layout = 'nologin';
$this->title = '提示';
?>
<div class="col-xs-12 col-sm-10 col-md-10">
	<div class="site-error">

		<h1>Forbidden (#403)</h1>
		<div class="alert alert-danger"><?php echo $message; ?>.</div>
		<button class="btn btn-primary" onclick="window.location.href = '/'"><i class="icon-home"></i> 返回首页</button>
		<button class="btn btn-primary" onclick="window.location.href = '<?php echo yii\helpers\Url::previous(); ?>'"><i class="icon-backward"></i> 返回上一页</button>

	</div>
</div>