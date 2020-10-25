<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <!--头部文件-->
    <meta charset="UTF-8">
    <title>{$title}-{$data.pcsname}</title>
    <meta name="author" content="www.sycit.cn, hyzwd@outlook.com"/>
    <link href="/favicon.ico" type="image/x-icon" rel="icon"/>
    <link href="/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="/assets/admin/css/console1412.css" rel="stylesheet" type="text/css" />
    <link href="/assets/admin/css/sycit.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/assets/plugins/jquery-2.2.4.min.js" ></script>
    <script type="text/javascript" src="/assets/plugins/bootstrap/js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="/assets/plugins/jquery-validation/js/jquery.validate.js"></script>
    <!--AJAX插件-->
    <script type="text/javascript" src="/assets/plugins/jquery.form.js" ></script>
    <!--弹窗插件-->
    <script type="text/javascript" src="/assets/plugins/toastr.js" ></script>
    <!-- bootstrap3-dialog -->
    <script type="text/javascript" src="/assets/plugins/b.dialog.js" ></script>
    <!-- layui -->
    <link rel="stylesheet" type="text/css" href="/assets/plugins/layui/css/layui.css">
    <script type="text/javascript" src="/assets/plugins/layui/layui.js"></script>

    <!--打印-->
    <script type="text/javascript" src="/assets/plugins/print/jQuery.print.js" ></script>
</head>
<body>
<div class="console-container">
    <div class="row">
        <div class="col-lg-12">
            <div class="console-title console-title-border clearfix">
                <div class="col-md-4 pull-left order-title">
                    <h3><span>{$title}</span></h3>
                    <span class="text-explode">|</span>
                    <span class="head-title">{$data.pcsname}</span>
                </div>
                <div class="col-md-5 text-center order-title">
                    {neq name="$data.status" value="-1"}
                    {eq name="$data.affirm" value="0"}
                    <input class="btn btn-primary" type="button" value="确认订单" onclick="ConfirmOrder('{$data.pnumber}');">
                    {else/}
                    <input class="btn btn-primary" type="button" value="订单已确认" disabled>
                    {/eq}
                    {if condition="($data.pshoudj == 1)"/}
                    <input class="btn btn-primary" type="button" value="已收订金" disabled>
                    {elseif condition="($data.pshoudj == 2)"/}
                    <input class="btn btn-primary" type="button" value="已收余款" disabled>
                    {elseif condition="($data.pshoudj == 3)"/}
                    <input class="btn btn-primary" type="button" value="已收全款" disabled>
                    {/if}
                    {if condition="($data.status == 5)"/}
                    <input class="btn btn-primary" type="button" value="订单已出库" disabled>
                    {/if}
                    {if condition="($Think.session.user_auth == 1) OR ($Think.session.user_auth == 4)"}
                    {if condition="($data.pshoudj == 0) AND ($data.affirm == 1)"}
                    <input class="btn btn-success" type="button" value="确认订金" onclick="ConfirmDingjin('{$data.pnumber}');">
                    {/if}
                    {if condition="($data.pshoudj == 1) AND ($data.status < 5)"}
                    <input class="btn btn-success" type="button" value="确认余款" onclick="QueRenYuKuan('{$data.pnumber}');">
                    {/if}
                    {/if}
                    {/neq}

                </div>
                <div class=" col-md-3 text-right">
                    <a class="btn btn-primary" onclick="PrintMytable();">打印预览</a>
                    <a href="javascript:window.close();" class="btn btn-default">
                        <span>关闭窗口</span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div style="padding: 15px;" id="orderList">
                <div class="order-head">
                    <div class="order-head-title"><span>{:Config('syc_webname')}</span></div>
                </div>
                <div class="form-inline">
                    <div class="row order-form-title">
                        <div class="syc-orde-col"><span></span></div>
                        <div class="syc-orde-col order-form-name">
                            <span>销售订单</span>
                        </div>
                        <div class="syc-orde-col text-right">
                            <label class="control-label syc-label">销售单号:</label>
                            <input type="text" class="syc-input w120 order-one" value="{$data.pnumber}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="syc-orde-col">
                            <label class="control-label syc-label order-span">客户名称:</label>
                            <input type="text" class="syc-input w120"  value="{$data.pcsname}" disabled>
                        </div>
                        <div class="syc-orde-col">

                        </div>
                        <div class="syc-orde-col" align="right">
                            <label class="control-label syc-label">销售日期:</label>
                            <input type="text" class="syc-input w120" value="{$data.pstart_date}" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="syc-orde-col text-left">
                            <label class="control-label syc-label">联系电话:</label>
                            <input type="text" class="syc-input w120" value="{$data.pcus_id.cus_moble}" disabled>
                        </div>
                        <div class="syc-orde-col text-left">
                            <label class="control-label syc-label order-span">收货地址:</label>
							{eq name="$data.pcus_id.cus_moble" value=""}
							<input type="text" class="syc-input disabled w250" disabled>
							{else/}
                            <input type="text" class="syc-input disabled w250" value="{$data.pcus_id.cus_prov}-{$data.pcus_id.cus_city}-{$data.pcus_id.cus_dist}" disabled>
							{/eq}
                        </div>
                        <div class="syc-orde-col text-right">
                            <label class="control-label syc-label">发货日期:</label>
                            <input type="text" class="syc-input w120" value="{$data.pend_date}" disabled>
                        </div>
                    </div>

                </div>

                <table class="table table-hover order-list" style="margin-bottom: 0;">
                    <tbody>
                    <tr class="thead">
                        <td rowspan="2" class="w50">序号</td>
                        <td rowspan="2" class="w80">颜色</td>
                        <td rowspan="2" colspan="2">产品编号</td>
                        <td colspan="3" class="w150">规格/mm</td>
                        <td rowspan="2" class="w50">吊脚高度/mm</td>
                        <td rowspan="2" colspan="2">包边线设置</td>
                        <td rowspan="2" class="w80">锁向</td>
                        <td rowspan="2" class="w80">锁具</td>
                        <td rowspan="2" class="w65">数量</td>
                        <td rowspan="2" class="w50">单价</td>
                        <td rowspan="2" class="w80">金额</td>
                        <td rowspan="2" class="w120">备注</td>
                    </tr>
                    <tr class="thead">
                        <td class="w50">宽</td>
                        <td class="w50">高</td>
                        <td class="w50">厚</td>
                    </tr>
                    <!--列表开始-->
                    {volist name="list" id="vo" empty="$empty" key="k"}
                    <tr>
                        <td>{$vo.xuhao}</td>
                        <td>{$vo.yanse}</td>
                        <td class="w50">{$vo.products}</td>
                        <td class="w50">{$vo.chanph}</td>
                        <td>{$vo.breadth}</td>
                        <td>{$vo.heiget}</td>
                        <td>{$vo.thick}</td>
                        <td>{$vo.diaojiao}</td>
                        <td width="50">{$vo.attribute}</td>
                        <td width="70">{$vo.baobian}</td>
                        <td>{$vo.suoxiang}</td>
                        <td>{$vo.fittings}</td>
                        <td>{$vo.quantity}</td>
                        <td class="sum" width="100">￥{$vo.unitPrice}</td>
                        <td class="sum" width="100">￥{$vo.amount}</td>
                        {eq name="$k" value="1"}<td rowspan="{$count}" width="120">{$remark}</td>{/eq}
                    </tr>
                    {/volist}
                    <!--列表结束-->
                    <tr>
                        <td rowspan="5" width="5%" style="font-size: 16px;"><span>订<br/>购<br/>声<br/>明</span></td>
                    </tr>
                    <tr><td class="text text-left" colspan="15" style="border-bottom: 0px;">1. 订货方需将订单签字回传并付30%货款，且订单生效。</td></tr>
                    <tr><td class="text text-left" colspan="15" style="border-bottom: 0px;border-top: 0px;">2. 订货方需确认好订购产品的具体颜色、款式、尺寸及相关细节（尺寸公差±2MM内属于正常范围）。</td></tr>
                    <tr><td class="text text-left" colspan="15" style="border-bottom: 0px;border-top: 0px;">3. 订单成立后将不能做任何修改，遇特殊紧急情况。请及时与我司联系协商。</td></tr>
                    <tr><td class="text text-left" colspan="15" style="border-top: 0px;">4. 订货方需在约定时间内及时将剩余货款付清，如遇延误我司将不承担任何责任。</td></tr>
                    </tbody>
                </table>

                <table class="table borde-xiao">
                    <tbody>
                    <!---->
                    <tr>
                        <td colspan="4">&nbsp;</td>
                        <td class="w350 text-left" colspan="5">
                            金额合计:
                            <span class="syc-input order-one w250" id="AmountBig"></span>
                        </td>
                        <td class="text-left" colspan="2">
                            数量合计:
                            <span class="syc-input order-one w50v"> {$data.pcount}</span>
                        </td>
                        <td class="text-left" colspan="4">
                            金额合计:
                            <span class="syc-input w150 order-one" id="AmountSmall"></span>
                        </td>
                    </tr>
                    <!---->
                    <tr style="height: 38px">
                        <td colspan="4">&nbsp;</td>
                        <td colspan="5" class="text-left">{eq name="$data.pyouhui" value="100"}
                            &nbsp;
                            {else /}
                            订单优惠:
                            <span class="syc-input w150 order-one"> {$data.pyouhui}%</span>
                            {/eq}</td>
                        <td colspan="1">&nbsp;</td>
                        <td colspan="5" class="text-left"><b>客户确认签名：</b></td>
                    </tr>
                    <!---->
                    <tr>
                        <td colspan="9">地址：{:Config('syc_address')}</td>
                        <td colspan="3">联系电话：{:Config('syc_webtel')}</td>
                        <td colspan="3">传真：{:Config('syc_webfax')}</td>
                    </tr>
                    <!---->
                    <tr>
                        <td colspan="3"></td>
                        <td colspan="3"><b>注：</b>左锁内开 <img src="/assets/img/suo-zn.png"></td>
                        <td colspan="3">右锁内开 <img src="/assets/img/suo-yn.png"></td>
                        <td colspan="3">左锁外开 <img src="/assets/img/suo-zw.png"></td>
                        <td colspan="3">右锁外开 <img src="/assets/img/suo-yw.png"></td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-inline" style="padding:0 15px;">
                <div class="alert alert-tip"><b>打印说明：</b>
                    <p></p>
                    <p>需要打印最好使用Charome(谷歌浏览器)、或360浏览器。</p>
                    <p>因为打印是调用系统接口，没有权限，所以默认打印会显示页眉和页脚。</p>
                    <p>如需要去除页眉和页脚，可以在浏览打印时候，点击【+更多设置】，取消勾选【页眉和页脚】。</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="/assets/admin/scripts/syc-order.js"></script>
<script type="text/javascript">
    $(function () {
        $("#AmountSmall").text(formatMoney({$data.pamount})); // 小写金额
        $("#AmountBig").text(smalltoBIG({$data.pamount})); // 大写金额
    });

    //
    var sycitcn = '{$Request.token}';
    var dingdanhao = '{$data.pnumber}';
    //确认订单
    function ConfirmOrder(e) {
        if (!isNaN(e) && e !== null && e !== '') {
            layui.use(['layer', 'form'], function(){
                var layer = layui.layer;
                layer.open({
                    offset: '150px',
                    type: 1, //窗口模式
                    title: false ,//不显示标题栏
                    area: '300px;',
                    closeBtn: false,
                    shade: 0.8, //遮罩层深度
                    content: '<div class="layui-msg">请确认客户是否已认可了订单，此操作后将不可修改订单，等待财务人员确认订金后进入生产流程。</div>',
                    id: 'LAY_layuipro', //设定一个id，防止重复弹出
                    btn: ['客户已确认', '没有确认'],
                    success: function(layero) {
                        var btn = layero.find('.layui-layer-btn');
                        btn.css('text-align', 'center');
                        btn.find('.layui-layer-btn0').attr({
                            href: '{:Url(\'handle/affirm\')}?handle=affirm&pid='+dingdanhao+'&sycitcn='+sycitcn,
                        });
                    }
                    ,btn2: function(index, layero){
                        layer.close(index);
                    }

                })
            })
        }
    };
    //打印设置
    function PrintMytable() {
        $("#orderList").print({mediaPrint : false,});
    };
    {if condition="($Think.session.user_auth == 1) OR ($Think.session.user_auth == 4)"}

    //确认订金
    function ConfirmDingjin(e) {
        if (!isNaN(e) && e !== null && e !== '') {
            layui.use(['layer', 'form'], function(){
                var layer = layui.layer;
                layer.open({
                    offset: '150px',
                    type: 1, //窗口模式
                    title: false ,//不显示标题栏
                    area: '300px;',
                    closeBtn: false,
                    shade: 0.8, //遮罩层深度
                    content: '<div class="layui-msg">请确认客户是否已经预付了收款，此操作后进入生产流程。</div>',
                    id: 'LAY_layuipro', //设定一个id，防止重复弹出
                    btn: ['已付订金', '没有订金'],
                    success: function(layero) {
                        var btn = layero.find('.layui-layer-btn');
                        btn.css('text-align', 'center');
                        btn.find('.layui-layer-btn0').attr({
                            href: '{:Url(\'handle/deposit\')}?handle=deposit&pid='+dingdanhao+'&sycitcn='+sycitcn,
                        });
                    }
                    ,btn2: function(index, layero){
                        layer.close(index);
                    }
                })
            })
        }
    }
    
    //确认余款
    function QueRenYuKuan(e) {
        if (!isNaN(e) && e !== null && e !== '') {
            layui.use(['layer', 'form'], function(){
                var layer = layui.layer;
                layer.open({
                    offset: '150px',
                    type: 1, //窗口模式
                    title: false ,//不显示标题栏
                    area: '300px;',
                    closeBtn: false,
                    shade: 0.8, //遮罩层深度
                    content: '<div class="layui-msg">请确认客户是否已经预付了收款，此操作后进入待出库流程。</div>',
                    id: 'LAY_layuipro', //设定一个id，防止重复弹出
                    btn: ['已付余款', '没有余款'],
                    success: function(layero) {
                        var btn = layero.find('.layui-layer-btn');
                        btn.css('text-align', 'center');
                        btn.find('.layui-layer-btn0').attr({
                            href: '{:Url(\'handle/balance\')}?handle=deposit&pid='+dingdanhao+'&sycitcn='+sycitcn,
                        });
                    }
                    ,btn2: function(index, layero){
                        layer.close(index);
                    }
                })
            })
        }
    }

    {/if}

</script>
</body>
</html>