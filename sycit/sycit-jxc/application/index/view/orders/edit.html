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
                                <form method="post" id="editwOrdersForm" name="editwOrdersForm">
                                    <input type="hidden" name="handle" value="edit">
                                    <input type="hidden" name="pid" value="{$data.pid}">
                                    <input type="hidden" name="__token__" value="{$Request.token}" />
                                    <div class="form-inline">
                                        <div class="syc-row row order-form-title">
                                            <div class="col-md-4"> </div>
                                            <div class="col-md-4 order-form-name">
                                                <span>销售订单</span>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <label class="control-label syc-label">销售单号:</label>
                                                <input type="text" class="syc-input w120 order-one" name="StrOrderOne" value="{$data.pnumber}" readonly>
                                            </div>
                                        </div>

                                        <div class="syc-row row">
                                            <div class="col-md-5">
                                                <label class="control-label syc-label order-span">客户名称:</label>
                                                <input type="text" class="syc-input w120"  value="{$data.pcsname}" disabled>
                                            </div>
                                            <div class="col-md-4"></div>
                                            <div class="col-md-3" align="right">
                                                <label class="control-label syc-label">销售日期:</label>
                                                <input type="text" class="syc-input w120" id="xiaoshouriqi" name="xiaoshouriqi" value="{$data.pstart_date}" readonly>
                                            </div>
                                        </div>

                                        <div class="row syc-row">
                                            <div class="col-md-4 text-left">
                                                <label class="control-label syc-label">联系电话:</label>
                                                <input type="text" class="syc-input w120" value="{$data.pcus_id.cus_moble}" disabled>
                                            </div>
                                            <div class="col-md-5 text-left">
                                                <label class="control-label syc-label order-span">收货地址:</label>
                                                <input type="text" class="syc-input disabled w250" value="{$data.pcus_id.cus_prov}-{$data.pcus_id.cus_city}-{$data.pcus_id.cus_dist}" disabled>
                                            </div>
                                            <div class="col-md-3 text-right">
                                                <label class="control-label syc-label">发货日期:</label>
                                                <input type="text" id="fahuoriqi" name="fahuoriqi" class="syc-input w120" value="{$data.pend_date}">
                                            </div>
                                        </div>

                                    </div>

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
                                            <th class="w50">宽</th>
                                            <th class="w50">高</th>
                                            <th class="w50">厚</th>
                                        </tr>
                                        </thead>
                                        <tbody id="orderList">
                                        {volist name="list" id="vo" empty="$empty"}
                                        <tr id="row_{$vo.xuhao}">
                                            <td id="Inputid_{$vo.xuhao}"><input type="hidden" name="Inputid[{$vo.xuhao}]" value="{$vo.xuhao}">{$vo.xuhao}</td>
                                            <td>
                                                <select name="Yanse[{$vo.xuhao}]" id="Yanse_{$vo.xuhao}" class="form-control">
                                                    <option></option>
                                                    {volist name="Color" id="voy"}
                                                    <option value="{$voy.pc_name}" {eq name="$vo.yanse" value="$voy.pc_name"}selected{/eq}>{$voy.pc_name}</option>
                                                    {/volist}
                                                </select>
                                            </td>
                                            <td class="w50">
                                                <span style="display:none;" id="Products_price_{$vo.xuhao}">{$vo.Products_price}</span>
                                                <select name="Products[{$vo.xuhao}]" id="Products_{$vo.xuhao}" class="form-control">
                                                    <option></option>
                                                    {volist name="Number" id="von"}
                                                    <option value="{$von.pn_name}" data-price="{$von.pn_price}" {eq name="$vo.products" value="$von.pn_name"} selected{/eq}>{$von.pn_name}</option>
                                                    {/volist}
                                                </select>
                                            </td>
                                            <td class="w50">
                                                <input type="text" class="form-control" name="Chanph[{$vo.xuhao}]" id="Chanph_{$vo.xuhao}" value="{$vo.chanph}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="Breadth[{$vo.xuhao}]" id="Breadth_{$vo.xuhao}" value="{$vo.breadth}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="Heiget[{$vo.xuhao}]" id="Heiget_{$vo.xuhao}" value="{$vo.heiget}">
                                                <input type="hidden" name="Mianji[{$vo.xuhao}]" id="Mianji_{$vo.xuhao}" value="{$vo.mianji}">
                                            </td>
                                            <td>
                                                <span style="display:none;" id="Thick_qhjc_{$vo.xuhao}">{$vo.Thick_qhjc}</span>
                                                <span style="display:none;" id="Thick_qhdz_{$vo.xuhao}">{$vo.Thick_qhdz}</span>
                                                <span style="display:none;" id="Thick_qhdzamo_{$vo.xuhao}">{$vo.Thick_qhdzamo}</span>
                                                <input type="text" class="form-control" name="Thick[{$vo.xuhao}]" id="Thick_{$vo.xuhao}" value="{$vo.thick}">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="Diaojiao[{$vo.xuhao}]" id="Diaojiao_{$vo.xuhao}" value="{$vo.diaojiao}">
                                            </td>
                                            <td>
                                                <select name="Attribute[{$vo.xuhao}]" id="Attribute_{$vo.xuhao}" class="form-control">
                                                    <option value="-">-</option>
                                                    {volist name="Baobian" id="vob"}
                                                    <option value="{$vob.ma_name}" {eq name="$vob.ma_name" value="$vo.attribute"} selected{/eq}>{$vob.ma_name}</option>
                                                    {/volist}
                                                </select>
                                            </td>
                                            <td>
                                                <span style="display:none;" id="Baobian_price_{$vo.xuhao}">{$vo.Baobian_price}</span>
                                                <select name="Baobian[{$vo.xuhao}]" id="Baobian_{$vo.xuhao}" class="form-control">
                                                    <option value="-">-</option>
                                                    {volist name="$vo.bsun" id="bs"}
                                                    <option value="{$bs.bval}" data-price="{$bs.bamo}" data-qhjc="{$bs.qhjc}" data-qhdz="{$bs.qhdz}" data-qhdzamo="{$bs.qhdzamo}" data-bid="{$bs.bid}" {eq name="$vo.baobian" value="$bs.bval"} selected{/eq}>{$bs.bval}</option>
                                                    {/volist}
                                                </select>
                                            </td>
                                            <td>
                                                <select name="Suoxiang[{$vo.xuhao}]" id="Suoxiang_{$vo.xuhao}" class="form-control">
                                                    <option value="-">-</option>
                                                    <option value="左锁内开" {eq name="$vo.suoxiang" value="左锁内开"} selected{/eq}>左锁内开</option>
                                                    <option value="左锁外开" {eq name="$vo.suoxiang" value="左锁外开"} selected{/eq}>左锁外开</option>
                                                    <option value="右锁内开" {eq name="$vo.suoxiang" value="右锁内开"} selected{/eq}>右锁内开</option>
                                                    <option value="右锁外开" {eq name="$vo.suoxiang" value="右锁外开"} selected{/eq}>右锁外开</option>
                                                </select>
                                            </td>
                                            <td>
                                                <span style="display:none;" id="Fittings_price_{$vo.xuhao}">{$vo.Fittings_price}</span>
                                                <select name="Fittings[{$vo.xuhao}]" id="Fittings_{$vo.xuhao}" class="form-control">
                                                    <option value="-">-</option>
                                                    {volist name="Fittings" id="vof"}
                                                    <option value="{$vof.lname}" data-price="{$vof.lprice}" {eq name="$vo.fittings" value="$vof.lname"} selected{/eq}>{$vof.lname}</option>
                                                    {/volist}
                                                </select>
                                            </td>
                                            <td class="add_chose input-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon reduce" onclick=setAmount.reduce("#Quantity_{$vo.xuhao}")>-</div>
                                                    <input type="text" class="text Quantity" name="Quantity[{$vo.xuhao}]" value="{$vo.quantity}" id="Quantity_{$vo.xuhao}" readonly>
                                                    <div class="input-group-addon add" onclick=setAmount.add("#Quantity_{$vo.xuhao}")>+</div>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control sum UnitPrice" name="UnitPrice[{$vo.xuhao}]" id="UnitPrice_{$vo.xuhao}" value="￥{$vo.unitPrice}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control sum Amount" name="Amount[{$vo.xuhao}]" id="Amount_{$vo.xuhao}" value="￥{$vo.amount}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="Remark[{$vo.xuhao}]" id="Remark_{$vo.xuhao}" value="{$vo.remark}">
                                            </td>
                                            </tr>
                                        {/volist}
                                        </tbody>
                                    </table>
                                    <table class="table syc-order-sum">
                                        <tbody>
                                        <tr>
                                            <td class="text-center">
                                                <label class="control-label syc-label">订单优惠:</label>
                                                <input type="text" name="Preferential" class="syc-input order-one w50" value="{if condition="($data.pyouhui elt 0) OR ($data.pyouhui egt 100)"}{else /}{$data.pyouhui}
                                                {/if}"><span>%</span>
                                            </td>
                                            <td class="text-center">
                                                <label class="control-label syc-label">订单大写金额:</label>
                                                <input type="text" name="AmountBig" class="syc-input order-one w250" value="" readonly>
                                            </td>
                                            <td class="text-center">
                                                <label class="control-label syc-label">订单数量:</label>
                                                <input type="text" name="OrderQuantity" class="syc-input order-one w50" value="{$data.pcount}" readonly>
                                            </td>
                                            <td class="text-center">
                                                <label class="control-label syc-label">订单小写金额:</label>
                                                <input type="text" name="AmountSmall" class="syc-input w150 order-one" value="" readonly>
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
                                            <div class="col-md-3">
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
                <div class="row">
                    <div class="col-lg-12 margin-top-3">
                        <div class="alert alert-tip"><b>修改订单说明：</b>
                            <p></p>
                            <p>本着严谨的设计，并不赞成可修改订单，最合适的是全部数据重新选择。</p>
                            <p>客户名称和信息、销售日期等主要信息是不可修改的，发货日期可更改。</p>
                            <p>如需更改数据，请先在【产品系列】重新选择，因为此时数据暂时锁定单价，不然单价是计算不出的。</p>
                            <p>【订单优惠】是默认【100】也即是没优惠，但在输入时只可输入【1-99】，不然保存默认【100】。</p>
                            <p>有个小技巧，未保存时，如果修改数据不对可点击【重置表格】再点加减数量，或刷新本页面。</p>
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
        <option value=""></option>
        {volist name="Baobian" id="vo"}
        <option value="{$vo.ma_name}">{$vo.ma_name}</option>
        {/volist}
    </div>
    <!--锁具-->
    <div id="AddSelectFittings">
        <option value=""></option>
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

        $("input[name='AmountSmall']").val(formatMoney('{$data.pamount}')); // 小写金额
        $("input[name='AmountBig']").val(smalltoBIG('{$data.pamount}')); // 大写金额

        //销售日期
        layui.use('laydate', function(){
            var laydate = layui.laydate;

            //执行一个laydate实例

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
                                url: '{:Url("orders/edit")}',
                                type: 'POST', //GET
                                data: $("#editwOrdersForm").serialize(),
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
                        var html = '<option></option>';
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
        return $("#editwOrdersForm").validate({
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