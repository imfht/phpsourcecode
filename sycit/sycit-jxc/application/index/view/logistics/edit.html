<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-full">
    {// 引入左侧导航文件}
    {include file="public/sidebar"}
    <!-- 主体内容 开始 -->
    <div class="viewFramework-product">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        <!-- 中间导航 结束 -->
        <div class="viewFramework-product-body">
            <div class="console-container">
                <!--内容开始-->
                <div class="row syc-bg-fff">
                    <div class="col-lg-12 syc-border-bs">
                        <div class="console-title">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                                <a href="javascript:history.go(-1);" class="btn btn-default">
                                    <span class="icon-goback"></span><span>返回</span>
                                </a>
                            </div>
                            <div class="pull-right">
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form class="form-horizontal" method="post" id="editLogisticsForm">
                            <table class="table contact-template-form">
                            <input type="hidden" name="__token__" value="{$Request.token}" />
                            <input type="hidden" name="log_id" value="{$data.log_id}" />
                                <tbody>
                                <tr>
                                    <td colspan="2">
                                        <div class="bs-callout bs-callout-warning">
                                            <span>{$title}</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        <span class="text-danger">*</span><span>物流名称：</span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w300 fleft" name="log_name" id="log_name" value="{$data.log_name}" disabled>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        <span class="text-danger">*</span><span>物流电话：</span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w300 fleft" name="log_phone" id="log_phone" value="{$data.log_phone}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        <span class="text-danger">*</span><span>物流传真：</span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control w300 fleft" name="log_fax" id="log_fax" value="{$data.log_fax}">
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">
                                        <span class="text-danger">*</span><span>详细地址：</span>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control fleft" name="log_address" id="log_address" value="{$data.log_address}" style="width: 50%;">
                                    </td>
                                </tr>

                                <tr class="table-submit">
                                    <td align="right"></td>
                                    <td>
                                        <button type="submit" class="btn btn-primary">提交信息</button>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </form>
                    </div>
                </div>
                <!--内容结束-->
            </div>
        </div>
    </div>
</div>

{// 引入底部公共JS文件}
{include file="public/footer"}
<script type="text/javascript" src="/assets/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-sales").addClass("sidebar-nav-active"); // 大分类
        $("#logis-index").addClass("active"); // 小分类

        //验证
        $("#editLogisticsForm").validate({
            rules: {
                log_name: {
                    required: true,
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

            errorClass: 'error', // 默认输入错误消息类
            focusInvalid: true, //当为false时，验证无效，没有焦点响应
            onkeyup: false, //当丢失焦点时才触发验证请求
            //errorElement: 'label',

            //验证不通过
            highlight: function(element, errorClass) {
                $(element).closest('.form-control').addClass(errorClass); // 验证未通过给input添加css
            },
            //验证通过后
            unhighlight: function (element, errorClass) {
                $(element).closest('.form-control').removeClass(errorClass); // 验证未通过给input添加css
            },
            success: function (label, errorClass) {
                label.closest('.form-control').removeClass(errorClass); // set success class to the control group
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
                    url: '{:Url("logistics/edit_do")}',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#editLogisticsForm").serialize(),
                    success: function (result) {
                        if (result.code > 0) {
                            toastr.success(result.msg)
                            window.setTimeout(function() {
                                window.location.href=result.url;
                            }, 1500);
                        } else {
                            toastr.error(result.msg);
                        }
                    }
                });
                return false; // 阻止表单自动提交事件
            }
        });
    })
</script>
</body>
</html>