<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" id="addLogisticsForm">
                <input type="hidden" name="__token__" value="{$Request.token}" />
                <div class="alert alert-warning success"></div>
                <div class="form-group">
                    <label for="log_name" class="col-sm-2 control-label"><span class="text-danger">*</span>名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="log_name" id="log_name" placeholder="物流名称">
                    </div>
                </div>
                <div class="form-group">
                    <label for="log_phone" class="col-sm-2 control-label"><span class="text-danger">*</span>电话</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="log_phone" id="log_phone" placeholder="物流电话">
                    </div>
                </div>
                <div class="form-group">
                    <label for="log_fax" class="col-sm-2 control-label"><span class="text-danger">*</span>传真</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="log_fax" id="log_fax" placeholder="物流传真">
                    </div>
                </div>
                <div class="form-group">
                    <label for="log_address" class="col-sm-2 control-label"><span class="text-danger">*</span>地址</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 fleft" name="log_address" id="log_address" placeholder="详细地址">
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

<script>
    $(document).ready(function () {
        $("#addLogisticsForm").validate({
            rules: {
                log_name: {
                    required: true,
                    remote:{
                        url:"check_name",
                        dataType: "json",           //接受数据格式
                        type:"post",
                        data: {                     //要传递的数据
                            name: function() {
                                return $("#log_name").val();
                            }
                        }
                    }
                },
                log_phone: {
                    required: true,
                    isPhone: true
                },
                log_fax: {
                    isPhone: true
                },
                log_address: {
                    required: true,
                },
            },
            messages: {
                log_name: {
                    remote: "已存在名称"
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
                    url: '{:Url("logistics/add_do")}',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#addLogisticsForm").serialize(),
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