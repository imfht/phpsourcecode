<?php

use yii\helpers\Html;
use app\helpers\MiscHelper;
use modules\doc\models\Doc;
use app\widgets\ZTree;
use yii\web\JsExpression;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $gmodel modules\doc\models\Doc */
/* @var $model modules\doc\models\Doc */

$this->title = $gmodel->title;
$this->params['breadcrumbs'][] = ['label' => 'Docs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>

    <?= Html::a('全部文档', ['index'], ['class' => 'btn btn-primary']) ?>

    <?= Html::a('添加页', ['create-page', 'gid' => $model->gid], ['class' => 'btn btn-success', 'data-modal-size' => 'modal-lg', 'data-toggle' => 'modal', 'data-target' => '#modal-dailog']) ?>

</p>
<div class="row">

    <div class="col-md-9 pull-right">
        <?php Pjax::begin(['id' => 'doc-content']) ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div style="padding: 30px">
                    <strong><?= $model->title ?></strong>
                    <hr />
                    <?= $model->content ?>
                </div>
            </div>
        </div>
        <?php Pjax::end() ?>
    </div>

    <div class="col-md-3">
        <div class="panel panel-default">
            <div id="district-tree" class="panel-body ztree">
                <?php
                $nodes = MiscHelper::listToTree(Doc::find()->where(['gid' => $model->gid])->asArray()->all(), 'id', 'pid', 'children');
                ZTree::widget([
                    'id' => 'district-tree',
                    'parentUserChildIds' => false,
                    'urlTarget' => '_self',
                    'expandAll' => true,
                    'urlParamValueUseChild' => true,
                    'url' => ['view', 'gid' => $gmodel->id],
                    'settings' =>  [
                        'data' => [
                            'key' => [
                                'name' => 'title'
                            ]
                        ],
                        // 'view' => [
                        //     'showIcon' => false
                        // ],
                        'callback' => [
                            'onClick' => new JsExpression("function(event,treeId,treeNode){
                                event.preventDefault();
                                $.pjax({url:treeNode.url,container: '#doc-content'});
                            }")
                        ],
                    ],
                    'nodes' => $nodes,
                ]); ?>
            </div>
        </div>
    </div>
</div>