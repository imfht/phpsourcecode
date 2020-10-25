<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <!--头部文件-->
    <meta charset="UTF-8">
    <title>{$title}-{$data.pcsname}-{:Config('syc_webname')}</title>
    <meta name="author" content="www.sycit.cn, hyzwd@outlook.com"/>
    <link href="/favicon.ico" type="image/x-icon" rel="icon"/>
    <link href="/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="/assets/admin/css/console1412.css" rel="stylesheet" type="text/css" />
    <link href="/assets/admin/css/sycit.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/assets/plugins/jquery-2.2.4.min.js" ></script>
    <script type="text/javascript" src="/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="/assets/plugins/jquery-validation/js/jquery.validate.js"></script>
    <!--AJAX插件-->
    <script type="text/javascript" src="/assets/plugins/jquery.form.js" ></script>
    <!--弹窗插件-->
    <script type="text/javascript" src="/assets/plugins/toastr.js" ></script>
    <!-- bootstrap3-dialog -->
    <script type="text/javascript" src="/assets/plugins/b.dialog.js" ></script>
    <!-- layui -->
    <link rel="stylesheet" type="text/css" href="/assets/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/assets/plugins/layui/layui.js"></script>

    <link href="/assets/plugins/fileinput/fileinput.css" rel="stylesheet" type="text/css" />
    <script src="/assets/plugins/fileinput/fileinput.js" type="text/javascript"></script>
    <script type="text/javascript" src="/assets/admin/scripts/syc-order.js"></script>

    <!--icheck-->
    <link href="/assets/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
    <script src="/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>

</head>
<body>
<div class="console-container">
    <div class="row">
        <div class="col-lg-12">
            <div class="console-title console-title-border clearfix">
                <div class="col-md-6 pull-left order-title">
                    <h3><span>{$title}</span></h3>
                    <span class="text-explode">|</span>
                    <span class="head-title">{$data.pcsname}</span>
                </div>
                <div class="col-md-6 text-right">
                <a href="javascript:window.close();" class="btn btn-default">
                    <span>关闭窗口</span></a>
                </div>
            </div>
        </div>
    </div>

    <!---->
    <div class="row">
        <div class="col-md-12">
            <div class="syc-table" style="padding: 15px;" id="orderList">
                <form class="form-horizontal" method="post" enctype="multipart/form-data" id="HandleAffirmEditForm">
                    <input type="hidden" name="sycitcn" value="{$Request.token}" />
                    <input type="hidden" name="handle" value="affirm"/>
                    <input type="hidden" name="pid" value="{$data.pnumber}"/>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">客户名称</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w300" value="{$data.pcsname}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">销售单号</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w300" value="{$data.pnumber}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">订单金额</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w300" id="pamount" value="{$data.pamount}" disabled>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">确认选择</label>
                        <div class="col-sm-4">
                            <div class="input-group">
                                <input type="checkbox" name="affirm_ok" value="1" class="form-control">
                                <label>客户已确认订单</label>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">上传图片</label>
                        <div class="col-sm-8">
                            <input id="input-4" name="pc_img" type="file" multiple class="file-loading form-control input-circle-right">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">备注信息</label>
                        <div class="col-sm-5">
                            <textarea class="form-control" name="remark" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="form-group modal-footer">
                        <div class="col-md-offset-2 pull-left">
                            <button type="submit" class="btn btn-primary"> 确认保存 </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    $(function () {
        $('input[name="affirm_ok"]').each(function(){
            var self = $(this),
                label = self.next(),
                label_text = label.text();

            label.remove();
            self.iCheck({
                checkboxClass: 'icheckbox_line-purple',
                radioClass: 'iradio_line',
                insert: '<div class="icheck_line-icon"></div>' + label_text
            });
        });
        //图片上传
        $("#input-4").fileinput({
            //uploadUrl: "/sycit.php/upload_img/index.html", //图片上传的地址
            allowedFileExtensions : ['jpg', 'png','gif'],//接收的文件后缀
            showUpload: false, //是否显示上传按钮
            showCaption: false,//是否显示标题
            showRemove: false,//是否显示删除
            //showRemove: false,//是否显示删除
            //预览图片的设置
            initialPreview: [
                "<img src='/uploads/noimage.png' class='file-preview-image' style='width:auto;height:100px;'>",
            ],
        });
        $("#pamount").val(formatMoney({$data.pamount})); // 小写金额

        //提交
        $("#HandleAffirmEditForm").validate({
            //
            rules: {
                affirm_ok: {
                    required: true,
                },
            },

            focusInvalid: true,
            onkeyup: false,
            //errorElement: 'div',
            //errorClass: "error",
            //sycError:false,
            // ,
            errorPlacement: function(error, element) {}, //设置验证消息不显示
            invalidHandler: function(){toastr.warning("没有选择确认");},
            submitHandler: function(form) {
                // 文件上传类 获取整个表单数据
                var formData = new FormData(form);
                //自定额外信息
                formData.append("CustomField", "This is some extra data");
                $.ajax({
                    url: "{:Url('handle/affirm')}",
                    type: 'POST',
                    data: formData,
                    processData: false, // 告诉jQuery不要去处理发送的数据
                    contentType: false, // 告诉jQuery不要去设置Content-Type请求头
                    success: function (result) {
                        if (result.code == '1') {
                            toastr.success(result.msg+"...正在关闭")
                            window.setTimeout(function(){
                                window.close(); //关闭本窗口页面
                            }, 2000);
                        } else {
                            toastr.error(result.msg+"...正在关闭");
                            window.setTimeout(function(){
                                window.close(); //关闭本窗口页面
                            }, 2000);
                        }
                    },
                    error: function (result) {
                        alert(result);
                    }
                });
                return false; // 阻止表单自动提交事件
            },
        });
    });
</script>
</body>
</html>