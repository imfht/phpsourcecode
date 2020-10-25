<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" id="editUserForm">
                <input type="hidden" name="__token__" value="{$Request.token}" />
                <input type="hidden" name="con_id" value="{$data.con_id}" />
                <div class="form-group">
                    <label for="inputname" class="col-sm-2 control-label"><span class="text-danger">*</span>姓名</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="con_name" id="inputname" value="{$data.con_name}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputmobile" class="col-sm-2 control-label"><span class="text-danger">*</span>手机号</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="con_mobile" id="inputmobile" value="{$data.con_mobile}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputpost" class="col-sm-2 control-label">职位</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="con_post" id="inputpost" value="{$data.con_post}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputqq" class="col-sm-2 control-label">QQ</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="con_qq" id="inputqq" value="{$data.con_qq}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputemail" class="col-sm-2 control-label">Email</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="con_email" id="inputemail" value="{$data.con_email}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputdescription" class="col-sm-2 control-label">备注信息</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="con_description" id="inputdescription" value="{$data.con_description}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">性别</label>
                    <div class="col-sm-10">
                        <label class="mt-radio">
                            <input type="radio" class="margin-right" name="con_sex" value="1" checked>男
                        </label>
                        <label class="mt-radio">
                            <input type="radio" class="margin-right" name="con_sex" value="2">女
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">保存</button>
                    <button type="reset" class="btn btn-default">重置</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        //表单验证
        $("#editUserForm").validate({
            rules: {
                con_name: {
                    required: true,
                    maxlength: 16,
                },
                con_mobile: {
                    required: true,
                    isMobile: true,
                },
            },

            //debug: true, // 调试时用，只验证不提交表单
            errorClass: 'help-block', // 默认输入错误消息类
            //errorLabelContainer: $(".success"), // 如果表单验证不通过，所有错误消息提示都会插入到该元素中
            //wrapper: "span", // 错误的标签
            focusInvalid: true, //当为false时，验证无效，没有焦点响应
            //onclick: true, //是否在鼠标点击时验证
            onkeyup: false, //当丢失焦点时才触发验证请求
            errorElement: 'label', //默认输入错误消息容器，有div和em/label
            //errorClass: "tooltip fade bottom in", //div错误的样式
            //sycError:true, //自己定义是否显示错误提示 需与:{errorElement: 'div',errorClass: "tooltip fade bottom in"} 一起用
            errorPlacement: function(error, element) {}, //设置验证消息不显示
            //验证不通过
            highlight: function(element, errorClass) {
                $(element).closest('.form-group').addClass('has-error'); // 验证未通过给input添加css
            },
            //验证通过后
            unhighlight: function (element) {
                $(element).closest('.form-group').removeClass('has-error'); // 验证未通过给input添加css
            },
            success: function (label) {
                label.closest('.form-group').removeClass('has-error'); // set success class to the control group
            },
            // 如果表单验证不通过
            invalidHandler: function(){
                //
                toastr.warning("填写不完整请认真检查");
            },
            // 表单验证成功，调用Ajax表单提交
            submitHandler: function(form) {
                //
                $.ajax({
                    url: '{:Url("contacts/edit_do")}',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#editUserForm").serialize(),
                    success: function (result) {
                        if (result.code > 0) {
                            toastr.success(result.msg)
                            window.setTimeout(function() {
                                bDialog.close({'url':result.url});
                            }, 1500);
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
                return false; // 阻止表单自动提交事件
            }
        });
    });
</script>
</body>
</html>