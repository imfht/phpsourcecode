<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/13 22:25
// +----------------------------------------------------------------------
// | TITLE: this to do?
// +----------------------------------------------------------------------
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>

<?php $this->beginBlock('head'); ?>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/jquery-ui.custom.css"/>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/chosen.css"/>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/datepicker.css"/>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/bootstrap-timepicker.css"/>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/daterangepicker.css"/>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/bootstrap-datetimepicker.css"/>
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/colorpicker.css"/>

    <!-- text fonts -->
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/ace-fonts.css"/>
    <!-- ace styles -->
    <link rel="stylesheet" href="<?= Url::base() ?>/aceAdmin/assets/css/ace.css" class="ace-main-stylesheet"
          id="main-ace-style"/>
    <!--[if lte IE 9]>
    <link rel="stylesheet" href="<?=Url::base()?>/aceAdmin/assets/css/ace-part2.css" class="ace-main-stylesheet"/>
    <![endif]-->

<?php $this->endBlock(); ?>
    <!--===========================================-->
    <!--html-->
    <div class="main-container" id="main-container">
        <!-- /section:basics/sidebar -->
        <div class="main-content">
            <div class="main-content-inner">

                <div class="page-content">

                    <div class="page-header">
                        <h1>
                            角色
                            <small>
                                <i class="ace-icon fa fa-angle-double-right"></i>
                                修改角色
                            </small>
                        </h1>
                    </div><!-- /.page-header -->

                    <div class="row">
                        <div class="col-xs-12">
                            <!-- PAGE CONTENT BEGINS -->
                            <?php $form =  ActiveForm::begin(
                                ['id' => 'admin-role-form',
                                    'action'=>Url::to(['admin-role/update','id'=>$model->id ]),
                                    'enableAjaxValidation' => true,
                                ])?>
                                <!-- #section:elements.form -->
                                <div class="row form-horizontal">
                                    <div class="col-xs-10 col-lg-offset-1">
                                        <div class="widget-box">
                                            <div class="widget-header">
                                                <h4 class="widget-title">修改角色</h4>
                                            </div>

                                            <div class="widget-body">
                                                <div class="widget-main">
                                                    <?= $form->field($model, 'name')
                                                        ->textInput(['id' => 'form-field-1','class'=>'col-md-offset-1  col-sm-3','placeholder'=>'角色名称'])
                                                        ->label('角色名称:',['class'=>'col-sm-3 control-label no-padding-right']) ?>
                                                    <?= $form->field($model, 'code')
                                                        ->textInput(['id' => 'form-field-1','class'=>'col-md-offset-1  col-sm-3','placeholder'=>'角色编号'])
                                                        ->label('角色编号:',['class'=>'col-sm-3 control-label no-padding-right']) ?>
                                                    <?= $form->field($model, 'des')
                                                        ->textInput(['id' => 'form-field-1','class'=>'col-md-offset-1  col-sm-3','placeholder'=>'描述'])
                                                        ->label('描述:',['class'=>'col-sm-3 control-label no-padding-right']) ?>

                                                    <div class="clearfix ">
                                                        <div class="col-md-offset-3 col-md-9">
                                                            <button class="btn btn-info ajaxForm" type="button">
                                                                <i class="ace-icon fa fa-check bigger-110"></i>
                                                                提交
                                                            </button>&nbsp;
                                                            <button class="btn" type="reset">
                                                                <i class="ace-icon fa fa-undo bigger-110"></i>
                                                                重置
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="hr hr-24"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div><!-- /.span -->
                                </div><!-- /.row -->
                            <?php ActiveForm::end();?>

                        </div><!-- /.col -->
                    </div><!-- /.row -->
                </div><!-- /.page-content -->
            </div>
        </div><!-- /.main-content -->


    </div><!-- /.main-container -->
    <!--html-->
    <!--===========================================-->
<?php $this->beginBlock('footer'); ?>

<?php $this->endBlock(); ?>

<script>
    $('.ajaxForm').bind('click',function () {
        var data = $('#admin-role-form').serialize();
        $.ajax({
            url:$('#admin-role-form').attr('action'),
            type:'post',
            dataType:'json',
            data:data,
            success:function (data) {
                layer.msg(data.message);
            }
        })
        
    });

</script>


