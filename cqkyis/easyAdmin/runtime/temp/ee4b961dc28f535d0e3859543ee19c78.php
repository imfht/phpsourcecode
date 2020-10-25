<?php /*a:1:{s:55:"E:\kyweixin\EasyAdmin\cqkyicms\admin\view\dept\add.html";i:1525928014;}*/ ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo htmlentities($name); ?></h4>
        </div>
        <form id="Add" method="post" action="<?php echo url('dept/add',array('id'=>$pmenu['dept_id'])); ?>" target="frame">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">上级部门</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="text"  class="form-control" value="<?php echo htmlentities($pmenu['dept_name']); ?>" disabled/>
                        <input type="hidden" name="parent_id" value="<?php echo htmlentities($pmenu['dept_id']); ?>" class="form-control" />
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">

                        <div class="form-group">
                            <label for="dept_name" class="control-label">部门名称</label>
                            <input type="text" class="form-control" id="dept_name" name="dept_name"  placeholder="部门名称">
                        </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  for="dept_cont" class="control-label">部门备注</label>
                        <textarea class="form-control" id="dept_cont" name="dept_cont"></textarea>

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
                dept_name: "required"

            },
            messages: {
                dept_name: "部门名称不能为空"

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