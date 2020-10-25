<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 10:15:24
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 15:01:42
 */
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\'); ?> */
/* @var $form yii\widgets\MyActiveForm */
?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)); ?>-form">

    <?= '<?php '; ?>$form = ActiveForm::begin(); ?>

<?php foreach ($generator->getColumnNames() as $attribute) {
    if (in_array($attribute, $safeAttributes)) {
        echo '    <?= '.$generator->generateActiveField($attribute)." ?>\n\n";
    }
} ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= '<?= '; ?>Html::submitButton(<?= $generator->generateString('保存'); ?>, ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?= '<?php '; ?>ActiveForm::end(); ?>

</div>
