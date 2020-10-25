<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-15 00:30:23
 * @Last Modified by:   Wang chunsheng  <2192138785@qq.com>
 * @Last Modified time: 2020-04-29 19:31:30
 */


use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$this->title = $model-><?= $generator->getNameAttribute() ?>;
$this->params['breadcrumbs'][] = ['label' => <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<?= "<?= " ?>$this->render('_tab') ?>


<div class=" firetech-main">
    <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

        <div class="panel panel-default">
            <div class="box-body">

                <p>
                    <?= "<?= " ?>Html::a(<?= $generator->generateString('更新') ?>, ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary']) ?>
                    <?= "<?= " ?>Html::a(<?= $generator->generateString('删除') ?>, ['delete', <?= $urlParams ?>], [
                    'class' => 'btn btn-danger',
                    'data' => [
                    'confirm' => <?= $generator->generateString('Are you sure you want to delete this item?') ?>,
                    'method' => 'post',
                    ],
                    ]) ?>
                </p>

                <?= "<?= " ?>DetailView::widget([
                'model' => $model,
                'attributes' => [
                <?php
                if (($tableSchema = $generator->getTableSchema()) === false) {
                    foreach ($generator->getColumnNames() as $name) {
                        echo "            '" . $name . "',\n";
                    }
                } else {
                    foreach ($generator->getTableSchema()->columns as $column) {
                        $format = $generator->generateColumnFormat($column);
                        echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                    }
                }
                ?>
                ],
                ]) ?>

            </div>
        </div>
    </div>
</div>