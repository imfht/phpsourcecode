<?php /*a:1:{s:62:"D:\php-work-2018\EasyAdmin\cqkyicms\admin\view\advert\add.html";i:1527233562;}*/ ?>
<style>
    #icon div:nth-child(2){width:100%!important;height:100%!important;}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            <h4 class="modal-title"><?php echo htmlentities($name); ?></h4>
        </div>
        <form id="Add" method="post" action="<?php echo url('advert/add'); ?>" target="frame">
        <div class="modal-body">

            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">广告名称</label>
                        <input type="hidden" name="__token__" value="<?php echo htmlentities(app('request')->token()); ?>" />
                        <input type="text" class="form-control required" id="advert_name" name="advert_name"  placeholder="广告名称">
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group ">
                        <label class="control-label" >商品分类 </label>

                            <input type="hidden" class="form-control" id="dept_id" name="cate_id"    placeholder="商品分类">
                            <input type="text" class="form-control required" name="dept_ids" id="dept_ids"    placeholder="商品分类">



                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">链接地址</label>

                        <input type="text" class="form-control" id="advert_urls" name="advert_urls"  placeholder="链接地址">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="start_time" class="control-label">开始时间</label>
                        <input type="text" class="form-control" id="start_time" name="start_time"  placeholder="开始时间">
                    </div>


                </div>
                <div class="col-md-6">

                        <div class="form-group">
                            <label for="end_time" class="control-label">结束时间</label>
                            <input type="text" class="form-control" id="end_time" name="end_time"  placeholder="联系电话">
                        </div>


                </div>
            </div>

            <div class="row">

            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label  class="control-label">广告图片</label>
                        <div class="input-group ">
                            <input type="text" id="good_img" name="advert_img" class="form-control required" placeholder="广告图片">
                            <span class="input-group-btn">
                                                         <div id="uploaders" class="wu-example">
                                                             <div id="icon" class="btn btn-info waves-effect waves-light"  >上传图片</div>

                                                         </div>
                                                        </span>
                        </div>
                    </div>
                </div>


            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group ">
                        <p  class="control-label">状态</p>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio1" value="0" name="status" >
                            <label for="inlineRadio1">禁用 </label>
                        </div>
                        <div class="radio radio-info radio-inline">
                            <input type="radio" id="inlineRadio2" value="1" name="status" >
                            <label for="inlineRadio2"> 启用 </label>
                        </div>

                    </div>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default waves-effect" data-dismiss="modal">关闭</button>
            <button type="submit" class="btn btn-info waves-effect waves-light">保存</button>
        </div>
        </form>
    </div>
</div>
<script src="/static/admins/js/jquery.min.js"></script>
<script src="/static/admins/js/bootstrap.min.js"></script>
<script src="/static/admins/js/jquery-validation/dist/jquery.validate.min.js"></script>
<script src="/static/admins/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script src="/static/admins/js/bootstrap-datepicker/js/bootstrap-datepicker.zh-CN.min.js"></script>
<link href="/static/admins/js/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css"/>
<script src="/static/admins/js/layer/layer.js"></script>
<link href="/static/admins/js/layui/css/layui.css" rel="stylesheet" type="text/css"/>
<link href="/static/admins/js/webuploader/webuploader.css" rel="stylesheet" type="text/css">
<script src="/static/admins/js/webuploader/webuploader.js"></script>
<script>
    $('#start_time').datepicker({
        language:"zh-CN",
        format:"yyyy-mm-dd",
         yearEnd:2050,        //设置最大年份
         autoclose: 1,
        keyboardNavigation: true,

    });
    $('#end_time').datepicker({
        language:"zh-CN",
        format:"yyyy-mm-dd",
        yearEnd:2050,
        autoclose: 1,

    });
    $().ready(function() {

        $("#Add").validate({

            messages: {
                advert_name: "广告名称不能为空",
                advert_img: "广告图片不能为空"
            },
            showErrors: function(errorMap, errorList) {

                $.each(errorList, function (i, v) {

                    layer.tips(v.message, v.element, {tips: [1, '#3595CC'], time: 2000 });
                    return false;
                });
                onfocusout: false
            }
        });

    });

    $.validator.setDefaults({
        submitHandler: function () {


           $('#Add').submit();

        }
    });


    $('#dept_ids').on('click',function () {
        layer.open({
            type: 2,
            area: ['300px', '450px'],
            fixed: false, //不固定
            maxmin: true,
            content: "<?php echo url('good/cate'); ?>",
            title:"选择分类"
        });

    });
    var uploaders = WebUploader.create({
        auto: true,
        swf: '/static/admin/js/uploader/Uploader.swf',
        server: "<?php echo url('upload/upload'); ?>",
        pick: '#icon',
        accept: {
            title: 'Images',
            extensions: 'gif,jpg,jpeg,bmp,png',
            mimeTypes: 'image/*'
        },
        formData: {
            face: 'advert',
            wight: 300,
            height: 200
        },
        fileNumLimit:1
    });

    uploaders.on('uploadAccept', function (fieldata, ret) {

        $('#good_img').val(ret.urls);
        //$('.input-group-addon').html("<img src='/uploads/"+ret.urls+"''/>");
    });
</script>