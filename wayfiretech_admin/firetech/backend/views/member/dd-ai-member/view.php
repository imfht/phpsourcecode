<?php
/**
 * @Author: Wang chunsheng  email:2192138785@qq.com
 * @Date:   2020-05-11 11:38:53
 * @Last Modified by:   Wang chunsheng  email:2192138785@qq.com
 * @Last Modified time: 2020-05-11 11:38:55
 */
 

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\DdAiMember */

$this->title = $model->user_id;
$this->params['breadcrumbs'][] = ['label' => '会员s', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<?= $this->render('_tab') ?>

<div class=" firetech-main">
<div class="dd-ai-member-view">

    <div class="panel panel-default">
        <div class="box-body">

    <p>
        <?= Html::a('更新', ['update', 'id' => $model->user_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->user_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'user_id',
            'open_id',
            'nickName',
            'avatarUrl',
            'gender',
            'country',
            'province',
            'city',
            'address_id',
            'wxapp_id',
            'create_time:datetime',
            'update_time:datetime',
        ],
    ]) ?>

</div>
    </div>
</div>
</div>