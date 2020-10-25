<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-15 00:29:56
 * @Last Modified by:   Wang chunsheng  <2192138785@qq.com>
 * @Last Modified time: 2020-04-29 19:31:23
 */


use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$modelClassName = Inflector::camel2words(StringHelper::basename($generator->modelClass));
$nameAttributeTemplate = '$model->' . $generator->getNameAttribute();
$titleTemplate = $generator->generateString('Update ' . $modelClassName . ': {name}', ['name' => '{nameAttribute}']);
if ($generator->enableI18N) {
    $title = strtr($titleTemplate, ['\'{nameAttribute}\'' => $nameAttributeTemplate]);
} else {
    $title = strtr($titleTemplate, ['{nameAttribute}\'' => '\' . ' . $nameAttributeTemplate]);
}

echo "<?php\n";
?>

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $title ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
$this->params['breadcrumbs'][] = <?= $generator->generateString('Update') ?>;
?>
<?= "<?= " ?>$this->render('_tab') ?>


<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">


                <?= '<?= ' ?>$this->render('_form', [
                'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>