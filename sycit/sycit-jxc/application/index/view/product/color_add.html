<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
    <link href="/assets/plugins/fileinput/fileinput.css" rel="stylesheet" type="text/css" />
    <script src="/assets/plugins/fileinput/fileinput.js" type="text/javascript"></script>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" id="AddProductColorForm" enctype="multipart/form-data">
                <input type="hidden" name="__token__" value="{$Request.token}" />
                <input type="hidden" name="handle" value="add"/>
                <div class="form-group">
                    <label for="pc_name" class="col-sm-2 control-label"><span class="text-danger">*</span>颜色名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300" name="pc_name" id="pc_name" placeholder="颜色名称">
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
                        <input type="text" class="form-control w300" name="pc_address" id="pc_address" placeholder="颜色产地">
                    </div>
                </div>
                <div class="form-group">
                    <label for="pc_description" class="col-sm-2 control-label">颜色简述</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300" name="pc_description" id="pc_description" placeholder="颜色简述">
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


        $("#AddProductColorForm").validate({
            //
            rules: {
                pc_name: {
                    required: true,
                    remote:{
                        url:"check_color",
                        dataType: "json",           //接受数据格式
                        type:"post",
                        data: {                     //要传递的数据
                            name: function() {
                                return $("#pc_name").val();
                            }
                        }
                    }
                },
            },
            messages: {
                pc_name: {remote:'已存在颜色名称'}
            },
            focusInvalid: true,
            onkeyup: false,
            errorElement: 'div',
            errorClass: "tooltip fade bottom in",
            sycError:true,
            highlight: function(element) {
                $(element).closest('.form-control').addClass('error');
            },
            unhighlight: function (element) {
                $(element).closest('.form-control').removeClass('error');
            },
            invalidHandler: function(){toastr.warning("填写不完整请认真检查");},
            submitHandler: function(form) {
                // 文件上传类 获取整个表单数据
                var formData = new FormData(form);
                //自定额外信息
                formData.append("CustomField", "This is some extra data");
                $.ajax({
                    url: "{:Url('product/color_add')}",
                    type: 'POST',
                    data: formData,
                    processData: false, // 告诉jQuery不要去处理发送的数据
                    contentType: false, // 告诉jQuery不要去设置Content-Type请求头
                    success: function (result) {
                        if (result.code == '1') {
                            toastr.success(result.msg)
                            window.setTimeout(function(){
                                bDialog.close({'title':'三叶草网络'});
                            }, 1000);
                        } else {
                            toastr.error(result.msg);
                            window.setTimeout(function(){
                                location.href=result.url;
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
    });
</script>
</body>
</html>