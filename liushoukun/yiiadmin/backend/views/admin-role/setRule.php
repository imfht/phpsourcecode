<?php
// +----------------------------------------------------------------------
// | When work is a pleasure, life is a joy!
// +----------------------------------------------------------------------
// | User: ShouKun Liu  |  Email:24147287@qq.com  | Time:2016/12/13 22:25
// +----------------------------------------------------------------------
// | TITLE: 设置权限
// +----------------------------------------------------------------------
use yii\helpers\Url;


$AdminRole = new \backend\models\AdminRole;
use yii\bootstrap\ActiveForm;
?>


<!-- /section:basics/navbar.layout -->
<div class="main-container" id="main-container">

    <!-- /section:basics/sidebar -->
    <div class="main-content">
        <div class="main-content-inner">

            <!-- /section:basics/content.breadcrumbs -->
            <div class="page-content">

                <!-- /section:settings.box -->
                <div class="page-header">
                    <h1>
                        角色管理
                        <small>
                            <i class="ace-icon fa fa-angle-double-right"></i>
                            设置权限
                        </small>
                    </h1>
                </div><!-- /.page-header -->

                <div class="row">

                    <div class="col-sm-8">
                        <h2>设置权限</h2>
                        <div id="treeview-checkable" class=""></div>
                    </div>
                </div>
                <div class="row">
                    <div class="clearfix ">
                        <div class="col-md-offset-3 col-md-9">
                            <button class="btn btn-info ajaxForm" type="button">
                                <i class="ace-icon fa fa-check bigger-110"></i>
                                确定
                            </button>&nbsp;
                        </div>
                    </div>
                </div>
                <?php $form =  ActiveForm::begin(
                    ['id' => 'setRule',
                        'action'=>Url::to(['set-rule','id'=>$model->id]),
                        'enableAjaxValidation' => true,
                    ])?>
                <?php ActiveForm::end();?>


            </div><!-- /.page-content -->
        </div>
    </div><!-- /.main-content -->


</div><!-- /.main-container -->
<?php $this->beginBlock('footer'); ?>

<!-- 增加的js -->
<script src="<?= Url::base() ?>/aceAdmin/assets/tree/src/js/bootstrap-treeview.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">

    $(function() {
        var defaultData = <?php echo json_encode($ruleAll)?>;
        //权限
        var rule = <?php echo json_encode($model->rule);?>;

        var $checkableTree = $('#treeview-checkable').treeview({
            data: defaultData,
            showIcon: false,
            showCheckbox: true,
            onNodeChecked: function(event, node) {
                rule.push( node.id)
            },
            onNodeUnchecked: function (event, node) {
                delete  rule[ $.inArray(node.id, rule)]
            }
        });


        /**
         *改变路由
         */
        function changeRule() {
            var data = Object();
            data._csrf=$('input[name=_csrf]').val();
            data.rule= rule;
            $.ajax({
                url:$('#setRule').attr('action'),
                type:'post',
                dataType:'json',
                data:data,
                success:function (data) {
                    layer.msg(data.message);
                }
            })
        }
        $('.ajaxForm').bind('click',function () {
            changeRule();
        })

    });
</script>
<?php $this->endBlock(); ?>





