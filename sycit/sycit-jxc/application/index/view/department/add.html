<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
</head>
<body>
<div class="col-md-12">
    <form class="form-horizontal" method="post" id="addDepartmentForm">
        <input type="hidden" name="__token__" value="{$Request.token}" />
        <div class="alert alert-warning success"></div>
        <div class="form-group" style="padding: 25px;">
            <label for="bumenname" class="col-sm-2 control-label"><span class="text-danger">*</span>部门名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control w300 fleft" name="bumenname" id="bumenname" placeholder="部门名称">
            </div>
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">保存</button>
            <button type="reset" class="btn btn-default">重置</button>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-config").addClass("sidebar-nav-active"); // 大分类
        $("#config-users").addClass("active"); // 小分类

        //表单验证
        $("#addDepartmentForm").validate({
            rules: {
                bumenname: {
                    required: true,
                    minlength: 2,
                    remote:{
                        url:"check_name",
                        dataType: "json",           //接受数据格式
                        type:"post",
                        data: {                     //要传递的数据
                            username: function() {
                                return $("#inputusername").val();
                            }
                        }
                    }
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 16

                },
                nickname: {
                    required: true,
                    remote:{
                        url:"check_nick",
                        dataType: "json",           //接受数据格式
                        type:"post",
                        data: {                     //要传递的数据
                            nickname: function() {
                                return $("#inputusernick").val();
                            }
                        }
                    }
                }
            },
            messages: {
                username: {
                    required: "名称不能为空",
                    minlength: $.validator.format("不能小于{0}个字符"),
                    remote: "已存在登录名称"
                }
            },

            //debug: true, // 调试时用，只验证不提交表单
            //errorClass: 'help-block', // 默认输入错误消息类
            //errorLabelContainer: $(".success"), // 如果表单验证不通过，所有错误消息提示都会插入到该元素中
            //wrapper: "span", // 错误的标签

            focusInvalid: true, //当为false时，验证无效，没有焦点响应
            //onclick: true, //是否在鼠标点击时验证
            onkeyup: false, //当丢失焦点时才触发验证请求
            errorElement: 'label', //默认输入错误消息容器，有div和em/label
            //errorClass: "tooltip fade bottom in", //div错误的样式
            //sycError:true, //自己定义是否显示错误提示 需与:{errorElement: 'div',errorClass: "tooltip fade bottom in"} 一起用

            //验证不通过
            highlight: function(element, errorClass) {
                $(element).closest('input').addClass('error').removeClass('valid'); // 验证未通过给input添加css
            },
            //验证通过后
            unhighlight: function (element) {
                $(element).closest('input').removeClass('error').addClass('valid'); // 验证未通过给input添加css
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
            submitHandler: function() {
                //
                $.ajax({
                    url: '{:Url("department/add_do")}',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#addDepartmentForm").serialize(),
                    success: function (result) {
                        if (result.code > 0) {
                            toastr.success(result.msg)
                            window.setTimeout(function() {
                                bDialog.close(result.url);
                            }, 1500);
                        } else {
                            toastr.error(result.msg);
                            window.setTimeout(function() {
                                window.location.href=result.url;
                            }, 1500);
                        }
                    }
                });
                //bDialog.close('提交成功') {:Url('department/add_do')};
                return false; // 阻止表单自动提交事件
            }
        });
    });

</script>
</body>
</html>