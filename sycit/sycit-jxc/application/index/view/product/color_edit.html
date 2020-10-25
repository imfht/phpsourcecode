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
                        <div class="portlet light margin-top-3">
                            <form class="form-horizontal" method="post" enctype="multipart/form-data" id="editProductColorForm">
                                <input type="hidden" name="__token__" value="">
                                <input type="hidden" name="handle" value="edit">
                                <input type="hidden" name="pc_id" value="{$data.pc_id}">
                                <div class="alert alert-warning alert-dismissible success" role="alert">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">颜色名称</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w300" value="{$data.pc_name}" disabled>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">图片介绍</label>
                                    <div class="col-sm-8">
                                        <input id="input-4" name="pc_img" type="file" multiple class="file-loading form-control input-circle-right">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pc_address" class="col-sm-2 control-label">颜色产地</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w300" name="pc_address" id="pc_address" value="{$data.pc_address}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="pc_description" class="col-sm-2 control-label">颜色简述</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control w300" name="pc_description" id="pc_description" value="{$data.pc_description}">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status" class="col-sm-2 control-label">状态</label>
                                    <div class="col-sm-10">
                                        <select class="form-control w200" name="status" id="status">
                                            <option value="1" {eq name="$data.status" value="1"} selected{/eq}>正常</option>
                                            <option value="2" {eq name="$data.status" value="2"} selected{/eq}>审核</option>
                                            <option value="0" {eq name="$data.status" value="0"} selected{/eq}>禁用</option>
                                            <option value="-1" {eq name="$data.status" value="-1"} selected{/eq}>删除</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group form-actions">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" class="btn btn-primary">保存</button>
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
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-product").addClass("sidebar-nav-active"); // 大分类
        $("#product-color").addClass("active"); // 小分类

        var $pc_img = '{$data.pc_img}',$image;
        if ($pc_img == '') {
            $image = '/uploads/noimage.png';
        } else {
            $image = $pc_img;
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

        //提交
        $("#editProductColorForm").validate({
            submitHandler: function(form) {
                // 文件上传类 获取整个表单数据
                var formData = new FormData(form);
                //自定额外信息
                formData.append("CustomField", "This is some extra data");
                $.ajax({
                    url: "{:Url('product/color_edit')}",
                    type: 'POST',
                    data: formData,
                    processData: false, // 告诉jQuery不要去处理发送的数据
                    contentType: false, // 告诉jQuery不要去设置Content-Type请求头
                    success: function (result) {
                        if (result.code == '1') {
                            toastr.success(result.msg)
                        } else {
                            toastr.error(result.msg);
                        }
                        window.setTimeout(function(){
                            //bDialog.close({'title':'三叶草网络'});
                            location.href=result.url;
                        }, 1000);
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