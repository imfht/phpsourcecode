<?php /*a:1:{s:56:"E:\kyweixin\EasyAdmin\cqkyicms\admin\view\user\edit.html";i:1526100088;}*/ ?>

<style>
    #icon div:nth-child(2){width:100%!important;height:100%!important;}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo htmlentities($name); ?></h4>
        </div>
        <form id="Add" method="post" action="<?php echo url('user/edit',['id'=>$vo['uid']]); ?>" target="frame">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label  class="control-label">用户名</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="text" class="form-control" id="username" value="<?php echo htmlentities($vo['username']); ?>" name="username"  placeholder="用户名">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label  class="control-label">用户密码</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="password" class="form-control" id="password" value="<?php echo htmlentities($vo['password']); ?>" name="password"  placeholder="用户密码">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="nickname" class="control-label">用户妮称</label>
                        <input type="text" class="form-control" id="nickname" value="<?php echo htmlentities($vo['nickname']); ?>" name="nickname"  placeholder="用户妮称">
                    </div>


                </div>
                <div class="col-md-6">

                        <div class="form-group">
                            <label for="phone" class="control-label">联系电话</label>
                            <input type="text" class="form-control" id="phone" value="<?php echo htmlentities($vo['phone']); ?>" name="phone"  placeholder="联系电话">
                        </div>


                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="role_id" class="control-label">所属角色</label>
                        <select class="select2 form-control" id="role_id" name="role_id">
                           <?php if(is_array($role) || $role instanceof \think\Collection || $role instanceof \think\Paginator): if( count($role)==0 ) : echo "" ;else: foreach($role as $key=>$var): ?>
                            <option value="<?php echo htmlentities($var['role_id']); ?>" <?php if($var['role_id'] == $vo['role_id']): ?> selected <?php endif; ?>><?php echo htmlentities($var['role_name']); ?></option>
                            <?php endforeach; endif; else: echo "" ;endif; ?>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label  for="dept_id" class="control-label">所属部门</label>
                        <input type="hidden" class="form-control" id="dept_id" name="dept_id" value="<?php echo htmlentities($vo['dept_id']); ?>"   placeholder="所属部门">
                        <input type="text" class="form-control" id="dept_ids" value="<?php echo htmlentities($vo['dept_name']); ?>"    placeholder="所属部门">
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
                            <!--<span class="input-group-addon"><i class="fa fa-user" id="fas"></i></span>-->
                            <input type="text" id="icons" name="face" class="form-control" placeholder="头像">
                                                        <span class="input-group-btn">
                                                            <div id="uploader" class="wu-example">
                                                                <div id="icon" class="btn btn-info waves-effect waves-light"  >上传头像</div>

                                                                </div>
                                                        </span>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">默认头像</label>
                        <div class="input-group m-t-10">

                            <span class="input-group-addon"><i class="fa fa-user" id="fas"></i></span>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group ">
                        <p  class="control-label">状态</p>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio1" value="0" name="status" <?php if($vo['status'] == '0'): ?> checked <?php endif; ?> >
                            <label for="inlineRadio1">禁用 </label>
                        </div>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio2" value="1" name="status" <?php if($vo['status'] == '1'): ?> checked <?php endif; ?> >
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
<link href="/static/admins/js/webuploader/webuploader.css" rel="stylesheet" type="text/css">
<script src="/static/admins/js/webuploader/webuploader.js"></script>


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

    });

    var uploader = WebUploader.create({
        swf: '/static/admins/js/webuploader/Uploader.swf',
        server: "<?php echo url('upload/upload'); ?>",// 后台路径
        pick: '#icon', // 选择文件的按钮。可选。内部根据当前运行是创建，可能是input元素，也可能是flash.
        resize: false,// 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
        chunked: true, // 是否分片
        duplicate: true,//去重， 根据文件名字、文件大小和最后修改时间来生成hash Key.
        chunkSize: 52428 * 100, // 分片大小， 5M
        fileSingleSizeLimit: 100 * 1024,//文件大小限制
        auto: true,
        // 只允许选择图片文件。
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/jpg,image/jpeg,image/png'
        },
        formData: {
            face: 'userface',
            w: 100,
            h: 100
        }
    });

    uploader.on('uploadAccept', function (fieldata, ret) {

        console.log(ret);
         $('#icons').val(ret.urls);
        $('.input-group-addon').html("<img src='/uploads/"+ret.urls+"''/>");

    });







</script>