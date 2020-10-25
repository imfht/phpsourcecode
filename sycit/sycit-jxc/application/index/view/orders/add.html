<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
    <script>
        //定义面积最小值
        window.AreaMinNum='{:Config("AreaMinNum")}';
    </script>
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-mini">
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
                    <div class="col-md-12 syc-border-bs">
                        <div class="console-title">
                            <div class="pull-left">
                                <h5>{$title}</h5>
                                <a href="javascript:history.go(-1);" class="btn btn-default">
                                    <span class="icon-goback"></span><span>返回</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="syc-page-panel margin-top-3">

                            <div class="syc-table" style="padding: 15px;">
                                <div class="order-head">
                                    <div class="order-head-title"><span>{:Config('syc_webname')}</span></div>
                                </div>
                                <form method="post" id="addOrdersForm" name="addOrdersForm">
                                    <input type="hidden" name="qiyename" value="">
                                    <input type="hidden" name="pcus_id" value="">
                                    <div class="form-inline">
                                        <div class="syc-row row order-form-title">
                                            <div class="col-md-4">&nbsp;</div>
                                            <div class="col-md-4 order-form-name">
                                                <span>销售订单</span>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <label class="control-label syc-label">销售单号:</label>
                                                <input type="text" name="StrOrderOne" class="syc-input w120 order-one" value="{$StrOrderOne}" readonly>
                                                <!--input type="text" class="syc-input w120 order-one" value="保存订单生成" readonly-->
                                            </div>
                                        </div>

                                        <div class="syc-row row">
                                            <div class="col-md-5">
                                                <label class="control-label syc-label order-span">客户名称:</label>
                                                <input type="text" id="kehumingcheng" name="kehumingcheng" class="syc-input w120"  value="" data-toggle="tooltip" data-placement="top" title="点击选择客户" style="cursor:pointer;" readonly>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="hidden" id="fahuowuliu" name="fahuowuliu" class="syc-input disabled" value="" disabled>
                                            </div>
                                            <div class="col-md-3" align="right">
                                                <label class="control-label syc-label">销售日期:</label>
                                                <input type="text" id="xiaoshouriqi" name="xiaoshouriqi" class="syc-input w120" value="{$shijian}">
                                            </div>
                                        </div>

                                        <div class="row syc-row">
                                            <div class="col-md-4 text-left">
                                                <label class="control-label syc-label">联系电话:</label>
                                                <input type="text" id="lianxidianhua" name="lianxidianhua" class="syc-input w120" value="" disabled>
                                            </div>
                                            <div class="col-md-5 text-left">
                                                <label class="control-label syc-label order-span">收货地址:</label>
                                                <input type="text" id="" name="shouhuodizhi" class="syc-input disabled w250" value="" disabled>
                                            </div>
                                            <div class="col-md-3 text-right">
                                                <label class="control-label syc-label">发货日期:</label>
                                                <input type="text" id="fahuoriqi" name="fahuoriqi" class="syc-input w120" value="">
                                            </div>
                                        </div>

                                    </div>

                                    <input type="hidden" name="__token__" value="{$Request.token}" />
                                    <table class="table order" style="margin-bottom: 0;">
                                        <thead>
                                        <tr>
                                            <th rowspan="2" class="w50">序号</th>
                                            <th rowspan="2" class="w80">颜色</th>
                                            <th rowspan="2" colspan="2" width="130">产品编号</th>
                                            <th colspan="3">规格/mm</th>
                                            <th rowspan="2" class="w65">吊脚高度/mm</th>
                                            <th rowspan="2" colspan="2">包边线设置</th>
                                            <th rowspan="2" width="85">锁向</th>
                                            <th rowspan="2" class="w100">锁具</th>
                                            <th rowspan="2" class="w80">数量</th>
                                            <th rowspan="2" class="w100">单价</th>
                                            <th rowspan="2" class="w100">金额</th>
                                            <th rowspan="2">备注</th>
                                        </tr>
                                        <tr>
                                            <th class="w65">宽</th>
                                            <th class="w65">高</th>
                                            <th class="w65">厚</th>
                                        </tr>
                                        </thead>
                                        <tbody id="orderList"><tr><td colspan="16" style="height: 30px;padding: 10px;"><span style="color: #9E9E9E;font-size: 18px;">还未添加产品颜色</span></td></tr></tbody>
                                    </table>
                                    <table class="table syc-order-sum">
                                        <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <label class="control-label syc-label">订单优惠:</label>
                                                <input type="text" name="Preferential" class="syc-input order-one w50"><span>%</span>
                                            </td>
                                            <td class="text-center">
                                                <label class="control-label syc-label">合计金额:</label>
                                                <input type="text" name="AmountBig" class="syc-input order-one w250" readonly>
                                            </td>
                                            <td class="text-center">
                                                <label class="control-label syc-label">数量合计:</label>
                                                <input type="text" name="OrderQuantity" class="syc-input order-one w50"  value="0" readonly>
                                            </td>
                                            <td class="text-center">
                                                <label class="control-label syc-label">合计金额:</label>
                                                <input type="text" name="AmountSmall" class="syc-input w150 order-one" value="0" readonly>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type='button' onclick="addRow()" value="增加一行" class="btn btn-primary">
                                                <input type='button' onclick="delRow()" value="删除一行" class="btn btn-danger">
                                            </div>
                                            <div class="col-md-9">
                                                <input type='button' id="submitHandleOrders" value="保存订单" class="btn btn-primary">
                                                <button type="reset" class="btn default">重置表格</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
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
<!--隐藏部分-->
<div style="display: none">
    <!--颜色-->
    <div id="AddSelectYanSe">
        <option value=""></option>
        {volist name="Color" id="vo"}
        <option value="{$vo.pc_name}">{$vo.pc_name}</option>
        {/volist}
    </div>
    <!--产品系列-->
    <div id="AddSelectProducts">
        <option value=""></option>
        {volist name="Number" id="vo"}
        <option value="{$vo.pn_name}" data-price="{$vo.pn_price}">{$vo.pn_name}</option>
        {/volist}
    </div>
    <!--包边属性-->
    <div id="AddSelectBaobian">
        <option value="-">-</option>
        {volist name="Baobian" id="vo"}
        <option value="{$vo.ma_name}">{$vo.ma_name}</option>
        {/volist}
    </div>
    <!--锁具-->
    <div id="AddSelectFittings">
        <option value="-">-</option>
        {volist name="Fittings" id="vo"}
        <option value="{$vo.lname}" data-price="{$vo.lprice}">{$vo.lname}</option>
        {/volist}
    </div>
</div>
<script type="text/javascript" src="/assets/admin/scripts/syc-order.js"></script>
<script type="text/javascript" src="/assets/plugins/jquery-validation/js/jquery.validate.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-sales").addClass("sidebar-nav-active"); // 大分类
        $("#sidebar-orders").addClass("active"); // 小分类
        $('[data-toggle="tooltip"]').tooltip(); //工具提示

        //
        var list = $("#orderList");

        var _Y=$("#AddSelectYanSe").html();
        var _P=$("#AddSelectProducts").html();
        var _B=$("#AddSelectBaobian").html();
        var _F=$("#AddSelectFittings").html();

        //默认添加表格行
        if ($("#AddSelectYanSe").children().size() !== 0) {
            list.children('tr').remove(); //先删除默认
            // 默认添加表格
            var itemsHtml = '';
            for (var k=1;k<6;k++) {
                itemsHtml += AddHtml(k,_Y,_P,_B,_F);
            }
            list.append(itemsHtml);
        };
        $('#fahuoriqi').val(getNewDay('{$shijian}','{:Config("delivery_period")}') || '');//发货日期相加时间数

        //销售日期
        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //执行一个laydate实例
            laydate.render({
                elem: '#xiaoshouriqi' //指定元素
                ,done: function(value, date, endDate){
                    //发货日期相加时间数
                    $('input[name="fahuoriqi"]').val(getNewDay(value,'{:Config("delivery_period")}') || '');
                }
            });
            laydate.render({
                elem: '#fahuoriqi' //指定元素
            });
        });


        //绑定优惠输入并计算
        $('input[name=Preferential]').watch(function(val) {
            if (val>=1 && val<=99) {
                calcProdSubTotal(true);
            } else {
                $('input[name=Preferential]').val('');
                calcProdSubTotal(false);
            }
        });

        //查询客户名称
        $("#kehumingcheng").click(function () {
            bDialog.open({
                title : '选择客户名称',
                width: '800',
                height: '630',
                url : '{:Url(\'orders/select_cusname\')}',
                callback:function(data){
                    if(data && data.results && data.results.length > 0 ) {
                       //console.log(data.results);
                        $('input[name="pcus_id"]').val(data.results[0].qid);
                        $('input[name="qiyename"]').val(data.results[0].qiye);
                        $('input[name="kehumingcheng"]').val(data.results[0].lxrmc);
                        $('input[name="lianxidianhua"]').val(data.results[0].lxrdh);
                        $('input[name="fahuowuliu"]').val(data.results[0].wlmc);
                        $('input[name="shouhuodizhi"]').val(data.results[0].shdz);
                    }
                }
            });
        });

        //提交订单
        $("#submitHandleOrders").click(function () {
            if (JqValidate()) {
                var AmountSmall = filterMoney($("input[name='AmountSmall']").val());
                if (AmountSmall <= 0) {
                    toastr.error('没有填写的订单请不要保存');
                    return false;
                }
                layui.use(['layer', 'form'], function(){
                    var layer = layui.layer;

                    layer.open({
                        //
                        title: '温馨提示',
                        content: '请确认提交的数据是否完整，如同行数据除备注<br>外其余由一处为空，都将删除整行数据。',
                        btn: ['我已确认', '重新修改'],
                        yes: function(index, layero){
                            //do something
                            layer.close(index);
                            $.ajax({
                                url: 'add_do',
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
    
    //产品下拉取得包边线设置
    function selectBaobian(_i,val) {
        //
        if (val !== '') {
            $.post('{:Url(\'orders/select_baobian\')}',
                {pid:val},
                function (data) {
                    if (data.code == '1') {
                        var option = data.data;
                        var html = '<option value="-">-</option>';
                        for (var i=0;i<option.length;i++) {
                            html += '<option value="'+option[i].bval+'" data-price="'+option[i].bamo+'" data-qhjc="'+option[i].qhjc+'" data-qhdz="'+option[i].qhdz+'" data-qhdzamo="'+option[i].qhdzamo+'" data-bid="'+option[i].bid+'">'+option[i].bval+'</option>';
                        }
                        //
                        $("#Baobian_"+_i).empty().append(html);
                    }
                }
            );
        }
    }

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