<?php

use yii\helpers\Url;
use yii\helpers\Html;
use backend\assets\AppAsset;
AppAsset::addScript($this,'@web/custom/user.js');
?>
<div class="card">
    <?php if(Yii::$app->session->get('msg')){ ?>
        <div class="alert alert-<?=Yii::$app->session->get('type')?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
            <?=Yii::$app->session->get('msg')?>
        </div>
        <?php Yii::$app->session->set('msg','');Yii::$app->session->set('type',''); ?>
    <?php }?>
    <?= Html::beginForm([Url::toRoute('/Admin/user/userupdate')],'post',['class'=>'form-horizontal']) ?>
    <div class="card-header">
        <h2>更新用户</h2>
    </div>
    <div class="card-body card-padding">
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">用户名</label>
            <div class="col-sm-5">
                <div <?php if(array_key_exists('username',$error)) echo 'class="form-group has-error"';?> >
                    <div class="fg-line">
                        <?php if(array_key_exists('username',$error)){ ?>
                            <label class="control-label" for="inputError1"><?php echo $error['username'][0] ?></label>
                        <?php }?>
                        <?= Html::input('hidden', 'id',isset($model->id) ? $model->id : '',['class' => 'form-control input-sm']) ?>
                        <?= Html::input('text', 'username',isset($model->username) ? $model->username : '',['class' => 'form-control input-sm','placeholder'=>'用户名']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">角色</label>
            <div class="col-sm-5">
                <select  name="roles[]" class="form-control input-sm select2" multiple>
                    <?php foreach($roleList as $item): ?>

                        <option value="<?=$item['name']?>" <?php if(in_array($item['name'],$roles)) echo 'selected'; ?>><?=$item['description']?></option>

                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
            <div class="col-sm-5">
                <div <?php if(array_key_exists('email',$error)) echo 'class="form-group has-error"';?> >
                    <div class="fg-line">
                        <?php if(array_key_exists('email',$error)){ ?>
                            <label class="control-label" for="inputError1"><?php echo $error['email'][0] ?></label>
                        <?php }?>
                        <?= Html::input('email', 'email',isset($model->email) ? $model->email : '',['class' => 'form-control input-sm','placeholder'=>'邮箱']) ?>
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