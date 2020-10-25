<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
</head>
<body>
<div class="col-md-12">
    <form class="form-horizontal" method="post" id="addUserForm">
        <input type="hidden" name="__token__" value="{$Request.token}" />
        <div class="alert alert-warning success"></div>
        <div class="form-group">
            <label for="inputusername" class="col-sm-2 control-label"><span class="text-danger">*</span>登陆名称</label>
            <div class="col-sm-10">
                <input type="text" class="form-control w300 fleft" name="username" id="inputusername" placeholder="登陆名称">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword" class="col-sm-2 control-label"><span class="text-danger">*</span>登陆密码</label>
            <div class="col-sm-10">
                <input type="text" class="form-control w300 fleft" name="password" id="inputPassword" placeholder="登陆密码">
            </div>
        </div>
        <div class="form-group">
            <label for="inputusernick" class="col-sm-2 control-label"><span class="text-danger">*</span>员工姓名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control w300 fleft" name="nickname" id="inputusernick" placeholder="员工姓名">
            </div>
        </div>
        <div class="form-group">
            <label for="inputemail" class="col-sm-2 control-label"><span class="text-danger">*</span>公司部门</label>
            <div class="col-sm-3">
                <select class="form-control w150" name="bumen">
                    <option value="">选择部门</option>
                    {volist name="group" id="vo"}
                    <option value="{$vo.id}">{$vo.title}</option>
                    {/volist}
                </select>
            </div>
            <label for="ruzhishijian" class="col-sm-2 control-label"><span class="text-danger">*</span>入职时间</label>
            <div class="col-sm-2">
                <input type="text" class="form-control w150 fleft" name="ruzhishijian" id="ruzhishijian" placeholder="入职时间">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">性别</label>
            <div class="col-sm-10">
                <label class="mt-radio">
                    <input type="radio" class="margin-right" name="sex" value="1" checked>男
                </label>
                <label class="mt-radio">
                    <input type="radio" class="margin-right" name="sex" value="1">女
                </label>
            </div>
        </div>
        <div class="form-group">
            <label for="inputemail" class="col-sm-2 control-label">员工邮箱</label>
            <div class="col-sm-10">
                <input type="text" class="form-control w300" name="email" id="inputemail" placeholder="员工邮箱">
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

        //
        layui.use('laydate', function() {
            var laydate = layui.laydate;
            //日期选择器
            laydate.render({
                elem: '#ruzhishijian'
                //,type: 'date' //默认，可不填
            });
        });
        
        //表单验证
        $("#addUserForm").validate({
            rules: {
                username: {
                    required: true,
                    minlength: 6,
                    maxlength: 16,
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
                },
                bumen: {
                    required: true
                }
            },
            messages: {
                username: {
                    required: "登陆名称不能为空",
                    minlength: $.validator.format("不能小于{0}个字符"),
                    remote: "已存在登录名称"
                },
                password: {
                    required: "登陆密码不能为空",
                    minlength: $.validator.format("不能小于{0}个字符")
                },
                nickname: {
                    required: "员工姓名不能为空",
                    remote: "已存在姓名"
                },
                bumen: {
                    required: '公司部门必须选择'
                }
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
                    url: '{:Url("users/user_do")}',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#addUserForm").serialize(),
                    success: function (result) {
                        if (result.code > 0) {
                            toastr.success(result.msg)
                            window.setTimeout(function() {
                                bDialog.close({'url':result.url});
                            }, 1500);
                        } else {
                            toastr.error(result.msg);
                            window.setTimeout(function() {
                                window.location.href=result.url;
                            }, 1500);
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