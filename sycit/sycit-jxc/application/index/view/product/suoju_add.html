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
            <form class="form-horizontal sycval" method="post" id="AddProductNumberForm" enctype="multipart/form-data">
                <input type="hidden" name="__token__" value="{$Request.token}" />
                <input type="hidden" name="handle" value="add"/>
                <div class="form-group">
                    <label for="lname" class="col-sm-2 control-label"><span class="text-danger">*</span>锁具名称</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300 text-left" name="lname" id="lname" placeholder="锁具名称">
                    </div>
                </div>
                <div class="form-group">
                    <label for="lprice" class="col-sm-2 control-label">设定金额</label>
                    <div class="col-sm-8">
                        <input type="text" class="form-control w100 text-left" name="lprice" id="lprice" placeholder="0.00">
                    </div>
                </div>
                <div class="form-group">
                    <label for="laddress" class="col-sm-2 control-label">锁具品牌</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300" name="laddress" id="laddress" placeholder="锁具品牌">
                    </div>
                </div>
                <div class="form-group">
                    <label for="ldescription" class="col-sm-2 control-label">锁具简述</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control w300" name="ldescription" id="ldescription" placeholder="锁具简述">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">图片介绍</label>
                    <div class="col-sm-8">
                        <input id="input-4" name="limg" type="file" multiple class="file-loading form-control input-circle-right">
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


        $("#AddProductNumberForm").validate({
            //
            rules: {
                lname: {
                    required: true,
                    remote:{
                        url:"check_suoju",
                        dataType: "json",           //接受数据格式
                        type:"post",
                        data: {                     //要传递的数据
                            name: function() {
                                return $("#lname").val();
                            }
                        }
                    }
                },
            },
            messages: {
                lname: {remote:'已存在名称'}
            },
            focusInvalid: true,
            onkeyup: false,
            errorElement: 'em',
            errorClass: "error",
            sycError:false,
            highlight: function(element,errorClass) {
                $(element).closest('.form-control').addClass(errorClass);
            },
            unhighlight: function (element,errorClass) {
                $(element).closest('.form-control').removeClass(errorClass);
            },
            invalidHandler: function(){toastr.warning("填写不完整请认真检查");},
            submitHandler: function(form) {
                // 文件上传类 获取整个表单数据
                var formData = new FormData(form);
                //自定额外信息
                formData.append("CustomField", "This is some extra data");
                $.ajax({
                    url: "{:Url('product/suoju_add')}",
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