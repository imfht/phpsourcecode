<?php /*a:1:{s:64:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\goodcate\add.html";i:1526126995;}*/ ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo htmlentities($name); ?></h4>
        </div>
        <form id="Add" method="post" action="<?php echo url('goodcate/add',array('id'=>$pmenu['cate_id'])); ?>" target="frame">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">上级分类</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="text"  class="form-control" value="<?php echo htmlentities($pmenu['cate_name']); ?>" disabled/>
                        <input type="hidden" name="parent_id" value="<?php echo htmlentities($pmenu['cate_id']); ?>" class="form-control" />
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">

                        <div class="form-group">
                            <label for="cate_name" class="control-label">分类名称</label>
                            <input type="text" class="form-control" id="cate_name" name="cate_name"  placeholder="菜单名称">
                        </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  for="remark" class="control-label">备注</label>
                        <textarea class="form-control" name="remark" id="remark" placeholder="备注"></textarea>

                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">排序</label>


                            <input type="text"  name="orderby" class="form-control" placeholder="排序">


                    </div>
                </div>


            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">关闭</button>
            <button type="submit" class="btn btn-info waves-effect waves-light">保存</button>
        </div>
        </form>
    </div>
</div>
<script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/static/admins/js/layer/layer.js"></script>
<script>
    $().ready(function() {

        $("#Add").validate({
            rules: {
                cate_name: "required"

            },
            messages: {
                cate_name: "产品分类不能为空"

            },
            showErrors: function(errorMap, errorList) {

                $.each(errorList, function (i, v) {

                    layer.tips(v.message, v.element, {tips: [1, '#3595CC'], time: 2000 });
                    return false;
                });
                onfocusout: false
            }
        });

    });

    $.validator.setDefaults({
        submitHandler: function () {


           $('#Add').submit();

        }
    });




</script>