<?php
use yii\helpers\Html;
$this->title = "Excel导入";
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h4 class="modal-title"><?= Html::encode($this->title) ?></h4>
</div>
<div class="modal-body">
    <?= Html::beginForm('','post',['enctype'=>'multipart/form-data']) ?>
        <div clsss="form-group">
            <input type="file" name="file" />
            <input type="submit" value="导入" class="btn btn-primary" />
        </div>
    <?= Html::endForm()?>
</div>