<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" id="editForm">
                <input type="hidden" name="__token__" value="{$Request.token}" />
                <div style="padding: 10px 0;">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">销售单号</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w300 fleft" name="pid" value="{$data.pnumber}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pstart_date" class="col-sm-2 control-label">销售日期</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w300 fleft" id="pstart_date" value="{$data.pstart_date}" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pend_date" class="col-sm-2 control-label">交货日期</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control w300 fleft" name="pend_date" id="pend_date" value="{$data.pend_date}" >
                            <label class="control-label">&nbsp;点击修改日期</label>
                        </div>
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
    $(function () {
        var index = parent.layer.getFrameIndex(window.name);
        layui.use(['layer', 'laydate'], function() {
            var layer = layui.layer;
            var laydate = layui.laydate;
            //发货日期
            laydate.render({
                elem: '#pend_date' //指定元素
                ,done: function(value, date, endDate){
                    //发货日期相加时间数
                    var pstart = $('#pstart_date').val();
                    var time = Date.parse(new Date(pstart)) / 1000; //销售日期
                    var time2 = Date.parse(new Date(value)) / 1000; //发货日期
                    if (time2 < time) {
                        toastr.warning('发货日期不能小于销售日期');
                    }
                }
            });
        });
        $('#editForm').validate({
            rules: {
                pend_date: {
                    required: true,
                },
            },

            focusInvalid: true,
            onkeyup: false,
            errorClass: "error",
            errorPlacement: function(error, element) {}, //设置验证消息不显示
            invalidHandler: function(){toastr.warning("没有填写完整");},
            submitHandler: function(form) {
                $.ajax({
                    url: '{:Url("schedule/in")}',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#editForm").serialize(),
                    success: function (result) {
                        if (result.code > 0) {
                            toastr.success(result.msg)
                            window.setTimeout(function() {
                                parent.$("#handle_status").val('1'); //给父层传递参苏
                                parent.layer.close(index); //关闭层
                            }, 1500);
                        } else {
                            toastr.error(result.msg);
                            window.setTimeout(function() {
                                parent.layer.close(index);
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