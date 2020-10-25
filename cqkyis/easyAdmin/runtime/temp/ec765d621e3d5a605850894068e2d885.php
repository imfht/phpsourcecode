<?php /*a:1:{s:55:"E:\kyweixin\EasyAdmin\cqkyicms\admin\view\menu\add.html";i:1525802021;}*/ ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo htmlentities($name); ?></h4>
        </div>
        <form id="Add" method="post" action="<?php echo url('menu/add',array('id'=>$pmenu['menu_id'])); ?>" target="frame">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">上级菜单</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="text"  class="form-control" value="<?php echo htmlentities($pmenu['menu_name']); ?>" disabled/>
                        <input type="hidden" name="parent_id" value="<?php echo htmlentities($pmenu['menu_id']); ?>" class="form-control" />
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">

                        <div class="form-group">
                            <label for="menu_name" class="control-label">菜单名称</label>
                            <input type="text" class="form-control" id="menu_name" name="menu_name"  placeholder="菜单名称">
                        </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  for="menu_role" class="control-label">权限标识</label>
                        <input type="text" class="form-control" id="menu_role" name="menu_role"  placeholder="权限标识">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">菜单图标</label>
                        <div class="input-group m-t-10">
                            <span class="input-group-addon"><i class="fa fa-user" id="fas"></i></span>
                            <input type="text" id="icons" name="menu_icon" class="form-control" placeholder="图标">
                                                        <span class="input-group-btn">
                                                        <button type="button" class="btn waves-effect waves-light btn-primary" id="icon">选择图标</button>
                                                        </span>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group ">
                        <p  class="control-label">属性</p>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio1" value="0" name="type" >
                            <label for="inlineRadio1">目录 </label>
                        </div>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio2" value="0" name="type" >
                            <label for="inlineRadio2"> 菜单 </label>
                        </div>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio3" value="2" name="type" >
                            <label for="inlineRadio3"> 按钮 </label>
                        </div>
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
                menu_name: "required",
                menu_role: "required"
            },
            messages: {
                menu_name: "菜单名称不能为空",
                menu_role: "菜单权限标识不能为空"
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


    $('#icon').on('click',function () {
        layer.open({
            type: 2,
            area: ['700px', '450px'],
            fixed: false, //不固定
            maxmin: true,
            content: "icon.html",
            title:"选择图标"
        });

    })

</script>