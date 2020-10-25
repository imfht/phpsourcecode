<?php

/**
 * @Author: Wang Chunsheng 2192138785@qq.com
 * @Date:   2020-03-30 21:44:22
 * @Last Modified by:   Wang Chunsheng 2192138785@qq.com
 * @Last Modified time: 2020-03-31 08:36:19
 */

use diandi\region\Region;
use common\helpers\LevelTplHelper;
use common\models\DdRegion;
use yii\helpers\Html;
use common\widgets\MyActiveForm;
use kartik\color\ColorInput;
use kartik\datetime\DateTimePicker;
use richardfan\widget\JSRegister;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\file\FileInput;
use kartik\select2\Select2;
use kartik\switchinput\SwitchInput;
use kartik\time\TimePicker;
use kartik\date\DatePicker;
use kartik\range\RangeInput;
use kartik\spinner\Spinner;
use kartik\touchspin\TouchSpin;
use kartik\growl\Growl;

$url = \yii\helpers\Url::toRoute(['get-region']);
/* @var $this yii\web\View */
/* @var $model backend\modules\bloc\models\Bloc */
/* @var $form yii\widgets\MyActiveForm */
?>




<div class="bloc-form">

    <?php $form = MyActiveForm::begin(); ?>

    <?= $form->field($model, 'province')->widget(\diandi\region\Region::className(), [
        'model' => $DdRegion,
        'url' => $url,
        'province' => [
            'attribute' => 'province',
            'items' => $DdRegion::getRegion(),
            'options' => ['class' => 'col-xs-4', 'prompt' => '选择省份']
        ],
        'city' => [
            'attribute' => 'city',
            'items' => $DdRegion::getRegion($model['province']),
            'options' => ['class' => 'col-xs-4', 'prompt' => '选择城市']
        ],
        'district' => [
            'attribute' => 'address_id',
            'items' => $DdRegion::getRegion($model['city']),
            'options' => ['class' => 'col-xs-4', 'prompt' => '选择县/区']
        ]
    ]); ?>

    <?=
        $form->field($model, 'username')->widget(FileInput::classname(), [
            'options' => ['accept' => 'image/*'],
        ]);

    ?>
    <?= $form->field($model, 'status')->widget(SwitchInput::classname(), [
        'type' => SwitchInput::CHECKBOX
    ]); ?>
    <?= $form->field($model, 'status')->widget(DateTimePicker::classname(), [
        'name' => 'start_time',
        'value' => '11:24 AM',
        'pluginOptions' => [
            'showSeconds' => true
        ]
    ]); ?>

    <?= $form->field($model, 'status')->widget(ColorInput::classname(), [
        'options' => ['placeholder' => 'Select color ...'],
    ]); ?>

    <?= $form->field($model, 'status')->widget(DatePicker::classname(), [
        'name' => 'check_issue_date',
        'value' => date('d-M-Y', strtotime('+2 days')),
        'options' => ['placeholder' => 'Select issue date ...'],
        'pluginOptions' => [
            'format' => 'yyyy-m-dd',
            'todayHighlight' => true
        ]
    ]); ?>

    <?= $form->field($model, 'status')->widget(RangeInput::classname(), [
        'options' => ['placeholder' => 'Select range ...'],
        'html5Options' => ['min' => 0, 'max' => 1, 'step' => 1],
        'html5Container' => ['style' => 'width:350px'],
        'addon' => ['append' => ['content' => 'star']],

    ]); ?>
    <?= TouchSpin::widget([
        'name' => 'volume',
        'options' => ['placeholder' => 'Adjust...'],
        'pluginOptions' => ['step' => 1]
    ]); ?>

    <?= Growl::widget([
        'type' => Growl::TYPE_SUCCESS,
        'icon' => 'glyphicon glyphicon-ok-sign',
        'title' => 'Note',
        'showSeparator' => true,
        'body' => 'This is a successful growling alert.'
    ]); ?>






    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

    </div>



    <div class="form-group">
        <div class="col-sm-offset-4 col-xs-4 col-sm-4 col-md-4 col-lg-4">
            <?= Html::submitButton('保存', ['class' => 'btn btn-success btn-block']) ?>
        </div>
    </div>

    <?php MyActiveForm::end(); ?>

</div>