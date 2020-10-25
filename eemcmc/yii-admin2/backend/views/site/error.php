<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */
$this->context->layout = 'nologin';
$this->title = $name;
?>
<div class="site-error">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
		<?= nl2br(Html::encode($message)) ?>
    </div>

	<button class="btn btn-primary" onclick="window.location.href = '/'"><i class="icon-home"></i> 返回首页</button>
	<button class="btn btn-primary" onclick="window.location.href = '<?php echo yii\helpers\Url::previous(); ?>'"><i class="icon-backward"></i> 返回上一页</button>

</div>
