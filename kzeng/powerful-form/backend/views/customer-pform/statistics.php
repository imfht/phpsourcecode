<?php

use yii\helpers\Html;
use yii\grid\GridView;

$this->title = '客户表单数据统计';
$this->params['breadcrumbs'][] = ['label' => '表单', 'url' => ['pform/index']];
$this->params['breadcrumbs'][] = $this->title;

$data = \backend\models\CustomerPform::statistic();
?>
<div class="customer-pform-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ( $data ) { ?>
        <?php 
            if( empty($data['data']) )
            {
        ?>
            <div class="alert alert-info" role="alert">到目前为止还没有收集到数据哟~</div>
        <?php
            }
            else
            {
        ?>
        <p>
            <?= Html::a('导出', ['exportstats', 'uid' => Yii::$app->request->get('uid')], ['class' => 'btn btn-success']) ?>
        </p>
        <table class='table'>
            <tr>
                <?php foreach ( $data['title'] as $column_title ) { ?>
                    <th><?php echo $column_title;?></th>
                <?php } ?>
            </tr>

            <?php foreach ( $data['data'] as $row_value ) { ?>
                <tr>
                    <?php foreach ( $row_value as $val ) { ?>
                        <td><?php echo str_replace("\n", "<br>", $val); ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </table>

        <?php } ?>

    <?php } ?>
</div>
