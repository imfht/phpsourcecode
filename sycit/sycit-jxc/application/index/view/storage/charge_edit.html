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
    <div class="viewFramework-product viewFramework-product-col-1">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        <div class="viewFramework-product-navbar">
            <div class="product-nav-stage product-nav-stage-main">
                <div class="product-nav-scene product-nav-main-scene">
                    <div class="product-nav-title">型材管理</div>
                    <div class="product-nav-list">
                        <ul>
                            <li class="active">
                                <a href="{:Url('storage/charge')}">
                                    <div class="nav-icon"></div><div class="nav-title">铝材管理</div>
                                </a>
                            </li>
                            <li>
                                <a href="{:Url('storage/bancailist')}">
                                    <div class="nav-icon"></div><div class="nav-title">板材管理</div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!--缩小展开-->
        <div class="viewFramework-product-navbar-collapse">
            <div class="product-navbar-collapse-inner" title="缩小/展开">
                <div class="product-navbar-collapse-bg"></div>
                <div class="product-navbar-collapse">
                    <span class="icon-collapse-left"></span>
                    <span class="icon-collapse-right"></span>
                </div>
            </div>
        </div>
        <!-- 中间导航 结束 -->
        <div class="viewFramework-product-body">
            <div class="console-container">
                <!--内容开始-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                                <a href="javascript:history.go(-1);" class="btn btn-default">
                                    <span class="icon-goback"></span><span>返回</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="portlet margin-top-3">
                            <form class="form-horizontal" method="post" id="form1">
                                <input type="hidden" name="pid" value="{$data.lxid}">
                                <div class="alert alert-warning alert-dismissible" role="alert">
                                    <strong>温馨提示</strong> 【KG/M】 和 【支长/M】 只能是整数或小数点后最多<B> 4 </B>位数，如：0.3542。
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>名称</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w300" value="{$data.lxname}" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">型号</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w300" name="lxxh" id="lxxh" value="{$data.lxxhao}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="lxkg" class="col-sm-2 control-label"><span class="text-danger">*</span>KG/M</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w300" name="lxkg" id="lxkg"  value="{$data.lxkg}" >
                                        <span>每米（M）的重量</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="lxzhic" class="col-sm-2 control-label"><span class="text-danger">*</span>支长/M</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w300" name="lxzhic" id="lxzhic"  value="{$data.lxzhic}" >
                                        <span>单支的总长度</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">图形</label>
                                    <div class="col-sm-8">
                                        <input id="input-4" name="lximg" type="file" multiple class="file-loading form-control input-circle-right">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="col-md-offset-2 col-md-8 left">
                                        <button type="submit" class="btn btn-primary">保 存</button>
                                        <button type="reset" class="btn btn-default">重 置</button>
                                    </div>
                                </div>
                            </form>
                        </div>
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
<link href="/assets/plugins/fileinput/fileinput.css" rel="stylesheet" type="text/css" />
<script src="/assets/plugins/fileinput/fileinput.js" type="text/javascript"></script>
<!--icheck-->
<link href="/assets/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
<script src="/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-storage").addClass("sidebar-nav-active"); // 大分类
        $("#storage-xingcai").addClass("active"); // 小分类

        //料型颜色
        $('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue', //颜色设置
            radioClass: 'iradio_square',
            increaseArea: '20%' // optional
        });

        var $pn_img = '{$data.lximg}',$image;
        if ($pn_img == '') {
            $image = '/uploads/noimage.png';
        } else {
            $image = $pn_img;
        }
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
                "<img src="+$image+" class='file-preview-image' style='width:auto;height:100px;'>",
            ],
        });

        //提交表单
        $("#form1").validate({
            //
            rules: {
                lxkg: {required: true,isNumber: true},
                lxzhic: {required: true,isNumber: true},
            },
            focusInvalid: true,
            onkeyup: false,
            errorElement: 'label',
            errorClass: "error",
            highlight: function(element, errorClass) {
                $(element).closest('.form-control').addClass(errorClass);
            },
            unhighlight: function (element, errorClass) {
                $(element).closest('.form-control').removeClass(errorClass);
            },
            //errorPlacement: function(error, element) {}, //设置验证消息不显示
            //invalidHandler: function(){toastr.warning("填写不完整请认真检查");},
            submitHandler: function(form) {
                // 文件上传类 获取整个表单数据
                var formData = new FormData(form);
                $.ajax({
                    url: "{:Url('storage/charge_edit')}",
                    type: 'POST',
                    data: formData,
                    processData: false, // 告诉jQuery不要去处理发送的数据
                    contentType: false, // 告诉jQuery不要去设置Content-Type请求头
                    success: function (result) {
                        if (result.code == '1') {
                            toastr.success(result.msg)
                            window.setTimeout(function(){
                                location.href=result.url;
                            }, 1000);
                        } else {
                            toastr.error(result.msg);
                            window.setTimeout(function(){
                                window.location.reload();
                            }, 1000);
                        }
                    },
                    error: function (result) {
                        alert(result);
                    }
                });
                return false; // 阻止表单自动提交事件
            },
        });
    })
</script>
</body>
</html>