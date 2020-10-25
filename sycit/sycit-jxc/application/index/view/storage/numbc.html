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
                    <div class="product-nav-title">进料管理</div>
                    <div class="product-nav-list">
                        <ul>
                            <li>
                                <a href="{:Url('storage/numlc')}">
                                    <div class="nav-icon"></div><div class="nav-title">铝材进料</div>
                                </a>
                            </li>
                            <li class="active">
                                <a href="{:Url('storage/numbc')}">
                                    <div class="nav-icon"></div><div class="nav-title">板材进料</div>
                                </a>
                            </li>
                            <li>
                                <a href="{:Url('storage/numpj')}">
                                    <div class="nav-icon"></div><div class="nav-title">配件进料</div>
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
                            <div class="pull-right">
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="marginTop20"></div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-tip">
                            <b>温馨提示：</b>
                            <p></p>
                            <p>请正确填写数字，为空、为0或不为数字将不修改库存，可输入带有 <B>减号</B> 相减。</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <form action="" id="addOrdersForm" name="addOrdersForm" method="post">
                            <input type="hidden" name="name" value="save">
                            <table class="table syc-table border order">
                            <thead>
                            <tr>
                                <th rowspan="2">产品编号</th>
                                <th width="80" rowspan="2">规格</th>
                                <th width="60" rowspan="2">总数量</th>
                                <th colspan="{$count}">颜&nbsp;&nbsp;色（支）</th>
                            </tr>
                            <tr>
                                {volist name="$name" id="vo" empty="<th>未添加颜色</th>"}
                                <th>{$vo.pc_name}</th>
                                {/volist}
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="$list" id="vo" empty="$empty"}
                            <tr>
                                <td width="80">{$vo.blname}</td>
                                <td>{$vo.bguige}</td>
                                <td>{$vo.zongshu}</td>
                                {volist name="$vo.sun" id="sun" empty="<td>&nbsp;</td>"}
                                <td><input type="text" class="form-control" name="bquantity[{$sun.bid}]" id="bquantity-{$sun.bid}" placeholder="{$sun.bquantity}"></td>
                                {/volist}
                            </tr>
                            {/volist}

                            </tbody>
                                <tfoot>
                                <tr>
                                    <td colspan="{$pagecol}">
                                        <div class="pull-right page-box">
                                            <button type="button" class="btn btn-primary" id="submitHandleOrders">保 存</button>
                                        </div>
                                    </td>
                                </tr>
                                </tfoot>
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
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-storage").addClass("sidebar-nav-active"); // 大分类
        $("#storage-jinliao").addClass("active"); // 小分类

        //提交订单
        $("#submitHandleOrders").click(function () {
            if (JqValidate()) {
                layui.use(['layer', 'form'], function(){
                    var layer = layui.layer;

                    layer.open({
                        //
                        title: '温馨提示',
                        content: '请确认是否提交库存数据，为 <b>0</b> 将不保存数据。',
                        btn: ['我已确认', '重新修改'],
                        yes: function(index, layero){
                            //do something
                            layer.close(index);
                            $.ajax({
                                url: 'numbc',
                                type: 'POST', //GET
                                data: $("#addOrdersForm").serialize(),
                                dataType:'json',    //返回的数据格式：json/xml/html/script/jsonp/text
                                success: function (result) {
                                    if (result.code == '1') {
                                        //
                                        toastr.success(result.msg)
                                        window.setTimeout(function() {
                                            window.location = result.url;
                                        }, 2000);
                                    } else {
                                        toastr.error(result.msg);
                                        window.setTimeout(function() {
                                            window.location.reload();
                                        }, 2000);
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
                return false;
            } else {
                toastr.warning("信息填写不完整");
            }
        });
    });
    //提交表单验证
    function JqValidate() {
        return $("#addOrdersForm").validate({
            rules:{
                kehumingcheng: {
                    required: true,
                },
            },
            errorClass: 'error', // 默认输入错误消息类
            focusInvalid: true, //当为false时，验证无效，没有焦点响应
            onkeyup: false, //当丢失焦点时才触发验证请求
            errorPlacement: function(error, element) {}, //设置验证消息不显示
            submitHandler: function(form) {
                //
            }
        }).form();
    };
</script>
</body>
</html>