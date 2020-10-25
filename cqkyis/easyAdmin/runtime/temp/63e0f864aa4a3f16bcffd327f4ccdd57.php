<?php /*a:1:{s:55:"E:\kyweixin\EasyAdmin\cqkyicms\admin\view\user\add.html";i:1526026274;}*/ ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo htmlentities($name); ?></h4>
        </div>
        <form id="Add" method="post" action="<?php echo url('user/add'); ?>" target="frame">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label  class="control-label">用户名</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="text" class="form-control" id="username" name="username"  placeholder="用户名">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label  class="control-label">用户密码</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="password" class="form-control" id="password" name="password"  placeholder="用户密码">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="nickname" class="control-label">用户妮称</label>
                        <input type="text" class="form-control" id="nickname" name="nickname"  placeholder="用户妮称">
                    </div>


                </div>
                <div class="col-md-6">

                        <div class="form-group">
                            <label for="phone" class="control-label">联系电话</label>
                            <input type="text" class="form-control" id="phone" name="phone"  placeholder="联系电话">
                        </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="role_id" class="control-label">所属角色</label>
                        <select class="select2 form-control" id="role_id" name="role_id">
                           <?php if(is_array($role) || $role instanceof \think\Collection || $role instanceof \think\Paginator): if( count($role)==0 ) : echo "" ;else: foreach($role as $key=>$vo): ?>
                            <option value="<?php echo htmlentities($vo['role_id']); ?>"><?php echo htmlentities($vo['role_name']); ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="dept_id" class="control-label">所属部门</label>
                        <input type="hidden" class="form-control" id="dept_id" name="dept_id"    placeholder="所属部门">
                        <input type="text" class="form-control" id="dept_ids"    placeholder="所属部门">
                    </div>
                </div>

            </div>
            <div class="row">

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">用户头像</label>
                        <div class="input-group m-t-10">
                            <span class="input-group-addon"><i class="fa fa-user" id="fas"></i></span>
                            <input type="text" id="icons" name="menu_icon" class="form-control" placeholder="图标">
                                                        <span class="input-group-btn">
                                                        <button type="button" class="btn waves-effect waves-light btn-primary" id="icon">上传头像</button>
                                                        </span>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group ">
                        <p  class="control-label">状态</p>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio1" value="0" name="status" >
                            <label for="inlineRadio1">禁用 </label>
                        </div>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio2" value="1" name="status" >
                            <label for="inlineRadio2"> 启用 </label>
                        </div>

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
<script src="/static/admins/js/select2/dist/js/select2.min.js" type="text/javascript"></script>
<link href="/static/admins/js/select2/dist/css/select2.css" rel="stylesheet" type="text/css">
<link href="/static/admins/js/select2/dist/css/select2-bootstrap.css" rel="stylesheet" type="text/css">
<script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/static/admins/js/layer/layer.js"></script>
<script>
    jQuery(".select2").select2({
        width: '100%'
    });
    $().ready(function() {

        $("#Add").validate({
            rules: {
                username: "required",
                password: "required"
            },
            messages: {
                username: "用户名不能为空",
                password: "用户密码不能为空"
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


    $('#dept_ids').on('click',function () {
        layer.open({
            type: 2,
            area: ['300px', '450px'],
            fixed: false, //不固定
            maxmin: true,
            content: "dept.html",
            title:"选择部门"
        });

    })

</script>