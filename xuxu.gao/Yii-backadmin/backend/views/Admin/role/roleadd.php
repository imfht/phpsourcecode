<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="card">
    <?php if(Yii::$app->session->get('msg')){ ?>
        <div class="alert alert-<?=Yii::$app->session->get('type')?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <?=Yii::$app->session->get('msg')?>
        </div>
        <?php Yii::$app->session->set('msg','');Yii::$app->session->set('type',''); ?>
    <?php }?>
    <?= Html::beginForm([Url::toRoute('/Admin/role/roleadd')],'post',['class'=>'form-horizontal']) ?>
    <div class="card-header">
        <h2>添加角色</h2>
    </div>
    <div class="card-body card-padding">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">角色名称</label>
            <div class="col-sm-5">
                <div <?php if(array_key_exists('name',$error)) echo 'class="form-group has-error"';?> >
                    <div class="fg-line">
                        <?php if(array_key_exists('name',$error)){ ?>
                            <label class="control-label" for="inputError1"><?php echo $error['name'][0] ?></label>
                        <?php }?>
                        <?= Html::input('text', 'name',isset($model->name) ? $model->name : '',['class' => 'form-control input-sm','placeholder'=>'角色名称']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">描述</label>
            <div class="col-sm-5">
                <div <?php if(array_key_exists('description',$error)) echo 'class="form-group has-error"';?> >
                    <div class="fg-line">
                        <?php if(array_key_exists('description',$error)){ ?>
                            <label class="control-label" for="inputError1"><?php echo $error['description'][0] ?></label>
                        <?php }?>
                        <?= Html::input('text', 'description',isset($model->description) ? $model->description : '',['class' => 'form-control input-sm','placeholder'=>'描述']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn bgm-blue waves-effect waves-button waves-float">提交</button>
            </div>
        </div>
    </div>
    <?= Html::endForm() ?>
</div>