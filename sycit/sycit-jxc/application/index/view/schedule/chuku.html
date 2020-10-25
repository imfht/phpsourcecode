<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header-model"}
    <!--icheck-->
    <link href="/assets/plugins/icheck/skins/all.css" rel="stylesheet" type="text/css" />
    <script src="/assets/plugins/icheck/icheck.min.js" type="text/javascript"></script>
</head>
<body>
<div class="container-fluid">
    <form class="form-horizontal" method="post" id="addLogisticsForm">
        <input type="hidden" name="__token__" value="{$Request.token}" />
        <input type="hidden" id="pstart_date" value="{$data.pstart_date}" />
        <div class="form-group">
            <label for="pnumber" class="col-sm-2 control-label">销售单号</label>
            <div class="col-sm-10">
                <input type="text" class="form-control w200" name="pid" id="pnumber" value="{$data.pnumber}" readonly>
            </div>
        </div>

        <div class="form-group">
            <label for="pend_date" class="col-sm-2 control-label">出库日期</label>
            <div class="col-sm-10">
                <input type="text" class="form-control w100 fleft" name="pend_date" id="pend_date" placeholder="年-月-日" >
                <label class="control-label">&nbsp;选择时间</label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">确认选择</label>
            <div class="col-sm-3">
                <div class="input-group">
                    <input type="checkbox" name="ok" value="1" class="form-control">
                    <label>选择出库</label>
                </div>
                <p></p>
                <code>订单已付款，可以出库</code>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">确  认</button>
            </div>
        </div>
    </form>
</div>

<script>
    $(document).ready(function () {
        //
        var mydate = new Date();
        var t=mydate.getFullYear();
        /*alert(t);*/
        $("#pend_date").text(t);
        //
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

        $('input[name="ok"]').each(function(){
            var self = $(this),
                label = self.next(),
                label_text = label.text();

            label.remove();
            self.iCheck({
                checkboxClass: 'icheckbox_line-red',//样式颜色
                radioClass: 'iradio_line',
                insert: '<div class="icheck_line-icon"></div>' + label_text
            });
        });
        $("#addLogisticsForm").validate({
            rules: {
                ok: {
                    required: true,
                },
                pend_date: {
                    required: true,
                },
            },

            //debug: true, // 调试时用，只验证不提交表单
            errorClass: 'help-block', // 默认输入错误消息类
            focusInvalid: true, //当为false时，验证无效，没有焦点响应
            onkeyup: false, //当丢失焦点时才触发验证请求
            errorElement: 'label', //默认输入错误消息容器，有div和em/label

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
            errorPlacement: function(error, element) {}, //设置验证消息不显示
            // 如果表单验证不通过
            invalidHandler: function(){
                //
                toastr.warning("填写不完整请认真检查");
            },
            // 表单验证成功，调用Ajax表单提交
            submitHandler: function(form) {
                //
                $.ajax({
                    url: '{:Url("schedule/chuku")}',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#addLogisticsForm").serialize(),
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
                                parent.layer.close(index); //关闭层
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