<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-15 00:30:04
 * @Last Modified by:   Wang chunsheng  <2192138785@qq.com>
 * @Last Modified time: 2020-04-29 19:32:40
 */


use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = <?= $generator->generateString('添加 ' . Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= "<?= " ?>$this->render('_tab') ?>

<div class="firetech-main">
    <div class="panel panel-default">
        <div class="box-body">
            <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-create">

                <?= "<?= " ?>$this->render('_form', [
                'model' => $model,
                ]) ?>

            </div>
        </div>
    </div>
</div>