<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-15 00:19:52
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-08-12 16:53:26
 */


use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?= $generator->enablePjax ? 'use yii\widgets\Pjax;' : '' ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>

<?= "<?= " ?>$this->render('_tab') ?>
                
<div class="firetech-main">

    <div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index ">
        <?= $generator->enablePjax ? "    <?php Pjax::begin(); ?>\n" : '' ?>
        <?php if (!empty($generator->searchModelClass)) : ?>
            <?= "    <?php " . ($generator->indexWidgetType === 'grid' ? " " : "// ") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php endif; ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">列表</h3>
            </div>
            <div class="box-body table-responsive">
                <?php if ($generator->indexWidgetType === 'grid') : ?>
                    <?= "<?= " ?>GridView::widget([
                    'dataProvider' => $dataProvider,
                    'layout' => "{items}\n{pager}",
                    <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
                    ['class' => 'yii\grid\SerialColumn'],

                    <?php
                    $count = 0;
                    if (($tableSchema = $generator->getTableSchema()) === false) {
                        foreach ($generator->getColumnNames() as $name) {
                            if (++$count < 6) {
                                echo "            '" . $name . "',\n";
                            } else {
                                echo "            //'" . $name . "',\n";
                            }
                        }
                    } else {
                        foreach ($tableSchema->columns as $column) {
                            $format = $generator->generateColumnFormat($column);
                            if (++$count < 6) {
                                echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                            } else {
                                echo "            //'" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                            }
                        }
                    }
                    ?>
                    
                    ['class' => 'common\components\ActionColumn'],
                    ],
                    ]); ?>
                <?php else : ?>
                    <?= "<?= " ?>ListView::widget([
                    'dataProvider' => $dataProvider,
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                    return Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
                    },
                    ]) ?>
                <?php endif; ?>

                <?= $generator->enablePjax ? "    <?php Pjax::end(); ?>\n" : '' ?>

            </div>
        </div>
    </div>
</div>