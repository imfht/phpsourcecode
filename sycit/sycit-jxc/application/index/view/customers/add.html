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
                <div class="row">
                    <div class="col-md-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5>{$title}</h5>
                                <a href="{:Url('customers/index')}">
                                    <button class="btn btn-default"><span class="icon-goback"></span>返回</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <form method="post" id="addCustomersForm">
                            <input type="hidden" name="__token__" value="{$Request.token}" />
                            <table class="table contact-template-form">
                                <tbody>
                                <tr>
                                    <td colspan="4">
                                        <div class="bs-callout bs-callout-warning">
                                            <span>客户信息</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%" class="right-color"><span>公司名称:</span></td>
                                    <td width="35%">
                                        <input type="text" class="form-control w300" name="name" id="name">
                                    </td>
                                    <td width="15%" class="right-color"><span class="text-danger">*</span><span>客户名称:</span></td>
                                    <td width="35%"><input type="text" class="form-control w300" name="duty" id="duty"></td>
                                </tr>
                                <tr>
                                    <td width="15%" class="right-color"><span>固话:</span></td>
                                    <td width="35%"><input type="text" class="form-control w300" name="phome" id="phome"></td>
                                    <td width="15%" class="right-color"><span>传真:</span></td>
                                    <td width="35%"><input type="text" class="form-control w300" name="fax" id="fax"></td>
                                </tr>
                                <tr>
                                    <td width="15%" class="right-color"><span>手机:</span></td>
                                    <td width="35%">
                                        <input type="text" class="form-control w300" name="moble" id="moble" >
                                    </td>
                                    <td width="15%" class="right-color"><span>邮编:</span></td>
                                    <td width="35%"><input type="text" class="form-control w300" name="code" id="code" ></td>
                                </tr>
                                <tr>
                                    <td width="15%" class="right-color"><span>邮箱:</span></td>
                                    <td width="35%"><input type="text" class="form-control w300" name="email" id="email"></td>
                                    <td width="15%" class="right-color"><span>网址:</span></td>
                                    <td width="35%"><input type="text" class="form-control w300" name="http" placeholder="http://"></td>
                                </tr>
                                <tr>
                                    <td width="15%" class="right-color"><span>详细地址:</span></td>
                                    <td width="35%" colspan="3" id="city_4">
                                        <select class="syc-select w150 prov" name="prov" id="selectProvince">
                                            <option>--请选择省份--</option>
                                        </select>
                                        <select class="syc-select w150 city" name="city" id="selectCitp">
                                        </select>
                                        <select class="syc-select w150 dist" name="dist" id="selectCounty">
                                        </select>
                                        <input type="text" class="form-control" style="margin-top: 10px;width: 50%;" name="street" placeholder="街道信息">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div class="bs-callout bs-callout-warning">
                                            <span>物流信息</span>
                                            <span class="pull-right"><a class="btn btn-primary" onclick="setHandle.addLogistics()">新增物流信息</a></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-right">发货物流：</td>
                                    <td colspan="3">
                                        <div class="pull-left">
                                            <input type="text" class="form-control w300" name="precusid" id="precusid" value="" readonly/>
                                            <input type="hidden" name="cus_log_id" id="cus_log_id" value=""/>
                                        </div>
                                        <label style="margin-left: 6px;"><a class="btn btn-primary" onclick="setHandle.selectLog()">选择物流</a></label>
                                    </td>
                                </tr>
                                <!--备注信息-->
                                <tr>
                                    <td colspan="4">
                                        <div class="bs-callout bs-callout-warning">
                                            <span>备注信息</span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="15%" class="right"><span>备注内容:</span></td>
                                    <td colspan="3"><textarea class="form-control" name="content" id="content" rows="6"></textarea> </td>
                                </tr>

                                <tr>
                                    <td align="right"></td>
                                    <td>
                                        <button type="submit" class="btn btn-primary">提交信息</button>
                                    </td>
                                    <td align="right"></td>
                                    <td align="right"></td>
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
<script type="text/javascript" src="/assets/plugins/city/jquery.cityselect.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        // 当前页面分类高亮
        $("#sidebar-sales").addClass("sidebar-nav-active"); // 大分类
        $("#sidebar-customers").addClass("active"); // 小分类
        $("#city_4").citySelect({prov:"北京市", city:"东城区", dist:""});

        //验证表单
        $("#addCustomersForm").validate({
            //
            rules:{
                duty: {
                    required:true
                },
            },
            focusInvalid: true, //当为false时，验证无效时，没有焦点响应
            //onclick: true, //是否在鼠标点击时验证
            onkeyup: false, //当丢失焦点时才触发验证请求
            //errorLabelContainer: '', //把错误信息统一放在一个容器里面
            //errorElement: 'div', //默认输入错误消息容器，有div和em
            //errorClass: "tooltip fade bottom in", //div错误的样式
            sycError:false, //自己定义是否显示错误提示
            errorPlacement: function(error, element) {}, //设置验证消息不显示
            highlight: function(element, errorClass) {
                $(element).closest('input').addClass('error'); // 验证未通过给input添加css
                $(element).closest('input').removeClass('valid'); // 验证未通过给input添加css
            },
            //如果表单验证不通过
            invalidHandler: function(element){
                toastr.warning("填写不完整请认真检查");
            },
            // 表单验证成功
            submitHandler: function() {
                //{:Url('customers/add_do')}
                //$("button[type=\"submit\"]").attr('disabled',true);
                //form.submit();
                var data = $("#addCustomersForm").serialize();
                $.sycToAjax('{:Url(\'customers/add_do\')}',data);
                return false; // 阻止表单自动提交事件
            }
        });
    });

    //onclick操作
    var setHandle = {
        //选择物流
        selectLog: function (e) {
            bDialog.open({
                title : '选择物流',
                width: '800',
                height: '700',
                url : '{:Url(\'logistics/select\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                        var logid = data.results[0].logid;
                        var logname = data.results[0].logname;
                        $('input[name="cus_log_id"]').val(logid);
                        $("#precusid").val(logname);
                    }
                }
            });
        },
        //新增物流无信息
        addLogistics: function (e) {
            bDialog.open({
                title : '新增物流',
                width: '800',
                height: '320',
                url : '{:Url(\'logistics/add\')}',
            });
        },
        //收货地址选择默认联系人
        setLogisticsUser: function (e) {
            var cusid = '';
            if (e!=='') {
                var data = {cusid:cusid};
                $.ajax({
                    url: '{:Url("premises/getContactName")}',
                    type: 'POST', //GET
                    data: data,
                    timeout:5000,    //超时时间
                    dataType:'json',
                    success: function (result) {
                        if (result.code == '1') {
                            //
                            $('input[name="pre_name"]').val(result.data.shr);
                            $('input[name="pre_phone"]').val(result.data.tel);
                            $('input[name="pre_street"]').val(result.data.street);
                            $("#city_shouhuo").citySelect({
                                prov:result.data.prov,
                                city:result.data.city,
                                dist:result.data.dist
                            });
                            //console.log(result)
                        } else {
                            toastr.warning(result.msg+" ...");
                        }
                    }
                });
                return false;
            } else {
                toastr.warning("请检查是否已设定默认联系人");
            }
        }
    };
</script>
</body>
</html>