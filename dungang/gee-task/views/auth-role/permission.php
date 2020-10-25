<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\widgets\ZTree;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\AuthRole */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '角色授权: ' . $model->name;
$this->params['breadcrumbs'][] = [
    'label' => '角色',
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = $model->name . '授权';
?>
<div class="auth-role-permissions">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php ActiveForm::begin(); ?>
    <div id="role-permission-tree" class="box-body ztree">
        <?php ZTree::widget([
            'id' => 'role-permission-tree',
            'expandAll'=>true,
            'settings' =>  [
                'callback' => [
                    'onCheck' => new JsExpression("function(event,treeId,treeNode){
                                    var treeObj = this.getZTreeObj(treeId);
                                    var nodes = treeObj.getCheckedNodes(true);
                                    var rights = new Array();
                                    for(var p in nodes){
                                        rights.push(nodes[p].id);
                                    }
                                    $('#permission_rights').val(rights.join(','));
                                }")
                ],
                'view' => [
                    'showIcon' => false
                ],
                'check' => [
                    'enable' => true
                ]
            ],
            'nodes' => $tree,
        ]); ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>