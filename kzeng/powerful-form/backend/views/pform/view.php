<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model backend\models\Pform */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '表单', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<style type="text/css">
    
</style>

<script>
    var ajaxUrl = "<?= Url::to(['/site/ajax-broker']); ?>";
</script>

<div class="pform-view">

    <h1>
        <?= Html::encode($this->title) ?>
        &nbsp;&nbsp;
        <button class="btn btn-success" data-toggle="modal" data-target="#myModal">新增字段 <i class="glyphicon glyphicon-plus"></i></button>
    </h1>

    <!--
    <p>
        <//?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <//?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '删除表单，确定?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    -->

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table table-striped detail-view'],
        'attributes' => [
            'id',
            'uid',
            'title',
            'description',
            [
               'attribute' => 'detail',
               'format'=> 'html',
            ],

            [
                'label' => '包含字段',
                'value' => $model->getFormField($model),
                'format'=> 'html',
            ],

            'created_at:datetime',
            'updated_at:datetime',
            //'user_id',
        ],
    ]) ?>


</div>



<!-- Modal -->
<div class="modal fade"  id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">新增字段</h3>
            </div>

            
            <div class="modal-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group field-product-title">
                            <h3><P><?= $model->title ?> </P></h3>
                            <p><?= $model->description ?></p>
                        </div>
                    </div>
                </div>

                <hr>
                <input type="hidden" id="form_uid" value="<?= $model->uid ?>">
                <div class="form-group">
                    <label class="control-label" for="field_title">字段名称</label>
                    <input type="text" id="field_title" class="form-control" name="field_title" maxlength="32" placeholder="填写字段名称，如 名字，手机号码 ...">
                    <div class="help-block"></div>
                </div>
              
                <div class="form-group">
                    <label class="control-label" for="field_type">字段类型</label>
                    <select class="form-control" id="field_type" name="field_type">
                    <option value="1">普通文本</option>
                    <option value="2">电话号码</option>
                    <option value="3">电子邮箱</option>
                    <option value="4">单项选择</option>
                    <option value="5">多项选择</option>
                    <option value="6">备注</option>
                    </select>
                    <div class="help-block"></div>
                </div>

                <div class="form-group field_value">
                    <label class="control-label" for="field_value">字段取值范围</label>
                    <input type="text" id="field_value" class="form-control" name="field_value" maxlength="255" placeholder="选项内容用空格分开，如喜欢的水果：西瓜 苹果 葡萄">
                    <div class="help-block"></div>
                </div>

                <div class="form-group">
                    <label class="control-label" for="field_placeholder">字段提示语</label>
                    <input type="text" id="field_placeholder" class="form-control" name="field_placeholder" maxlength="255">
                    <div class="help-block"></div>
                </div>

                <div class="form-group hide">
                    <label class="control-label" for="field_order">排序</label>
                    <input type="text" id="field_order" class="form-control" name="field_order" maxlength="32">
                    <div class="help-block"></div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                <button type="button" class="btn btn-success" id="addMetadata">确定</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script type="text/javascript">
    $('.field_value').hide();

    var form_uid;
    var field_title;
    var field_type;
    var field_value;
    var field_placeholder;
    var field_order;


    function addMetadataAjax() {
        var args = {
            'classname': '\\backend\\models\\Pform',
            'funcname': 'addMetadataAjax',
            'params': {
                'form_uid': form_uid,
                'field_title': field_title,
                'field_type': field_type,
                'field_value': field_value,
                'field_placeholder': field_placeholder,
                'field_order': field_order,
            }
        };
        $.ajax({
            url: ajaxUrl,
            type: 'GET',
            cache: false,
            dataType: 'json',
            data: 'args=' + JSON.stringify(args),
            success: function (ret) {
                location.reload();
            },
            error: function () {
            }
        });
    }

    $('#addMetadata').click (function () {
        form_uid = $('#form_uid').val();
        field_title = $('#field_title').val();
        field_type = $('#field_type').val();
        field_value = $('#field_value').val();
        field_placeholder = $('#field_placeholder').val();
        field_order = $('#field_order').val();

        //alert(form_uid+"--"+field_title+"--"+field_type+"--"+field_value+"--"+field_placeholder+"--"+field_order);

        addMetadataAjax();
    });


    $('#field_type').change (function () {
        if($('#field_type').val() > 3)
            $('.field_value').show();
        else
            $('.field_value').hide();
    });


</script>