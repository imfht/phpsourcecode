<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
    <style>
        .icheck-list label {
            margin-right: 10px;
        }
        .icheck-list input {
            width: 50px;
            text-align: center;
        }
    </style>
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
                <div class="marginTop10"></div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-tip">
                            <b>说明：</b>
                            <p></p>
                            <p>数量只可输入最多两位数和一位小数点5数字</p>
                        </div>
                        <div class="portlet margin-top-3">
                            <form class="form-horizontal" method="post" id="form1">
                                <div class="form-group">
                                    <label for="ms_pnid" class="col-sm-2 control-label"><span class="text-danger">*</span>系列</label>
                                    <div class="col-sm-10">
                                        <select class="form-control w300 fleft" name="ms_pnid" id="ms_pnid">
                                            <option value="">选择产品系列</option>
                                            {volist name="$number" id="vo" empty="<option value=>未添加</option>"}
                                            <option value="{$vo.pn_name}">{$vo.pn_name}</option>
                                            {/volist}
                                        </select>
                                        <span class="form-span">产品系列</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ms_blname" class="col-sm-2 control-label"><span class="text-danger">*</span>编号</label>
                                    <div class="col-sm-10">
                                        <select class="form-control w300 fleft" name="ms_blname" id="ms_blname">
                                            <option value="">选择板材名称</option>
                                            {volist name="$bancai" id="vo" empty="<option value=>未添加</option>"}
                                            <option value="{$vo.blname}">{$vo.blname}</option>
                                            {/volist}
                                        </select>
                                        <span class="form-span">板材名称</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ms_maname" class="col-sm-2 control-label"><span class="text-danger">*</span>属性</label>
                                    <div class="col-sm-10">
                                        <select class="form-control w300 fleft" name="ms_maname" id="ms_maname">
                                            <option value="">选择属性</option>
                                            {volist name="$attribute" id="vo" empty="<option value=>未添加</option>"}
                                            <option value="{$vo.ma_name}">{$vo.ma_name}</option>
                                            {/volist}
                                        </select>
                                        <span class="form-span">包边线属性</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ms_baobian" class="col-sm-2 control-label"><span class="text-danger">*</span>包边</label>
                                    <div class="col-sm-10">
                                        <select class="form-control w300 fleft" name="ms_baobian" id="ms_baobian">
                                            <option value="">选择包边</option>
                                            <option value="单包边">单包边</option>
                                            <option value="双包边">双包边</option>
                                        </select>
                                        <span class="form-span">单包或双包</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label"><span class="text-danger">*</span>料型</label>
                                    <div class="col-sm-10">
                                        <div class="input-group">
                                            <div class="icheck-list">
                                                {volist name="$liaox" id="vo" empty="<label>暂无数据</label>"}
                                                <div class="list"><label><input type="checkbox" name="liaox[{$vo.lxid}]" class="icheck"> {$vo.lxname}</label> <span> 数量 </span><input type="text" name="name[{$vo.lxid}]" value=""></div>
                                                {/volist}
                                            </div>
                                        </div>
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
        $("#sidebar-config").addClass("sidebar-nav-active"); // 大分类
        $("#config-shengchan").addClass("active"); // 小分类

        $('.icheck').each(function(){
            var self = $(this),
                label = self.next();

            //label.remove();
            self.iCheck({
                labelHover : false,
                cursor : true,
                checkboxClass : 'icheckbox_square-orange',
                radioClass : 'iradio_square-blue',
                increaseArea : '20%'
            });
        });

        //提交表单
        $("#form1").validate({
            rules: {
                ms_pnid: {required: true},
                ms_blname: {required: true},
                ms_maname: {required: true},
                ms_baobian: {required: true},
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
                layui.use(['layer', 'form'], function(){
                    var layer = layui.layer;

                    layer.open({
                        //
                        title: '温馨提示',
                        content: '请确认是否提交数据，需要慎重填写。',
                        btn: ['我已确认', '重新修改'],
                        yes: function(index, layero){
                            //do something
                            layer.close(index);
                            $.ajax({
                                url: "{:Url('config/produce_add')}",
                                type: 'POST', //GET
                                data: $("#form1").serialize(),
                                dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
                                success: function (result) {
                                    if (result.code == '1') {
                                        //
                                        toastr.success(result.msg)
                                        window.setTimeout(function() {
                                            window.location = result.url;
                                        }, 2000);
                                    } else {
                                        toastr.warning(result.msg);
                                    }
                                }
                            });
                            return false;
                        }
                        ,btn2: function(index, layero){
                            layer.close(index);
                        }
                    });
                });
                return false; // 阻止表单自动提交事件
            }
        });
    })
</script>
</body>
</html>