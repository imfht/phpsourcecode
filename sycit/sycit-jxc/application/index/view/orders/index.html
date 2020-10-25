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
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                            </div>
                            <div class="pull-right">
                                <a class="btn btn-primary" href="{:Url('orders/add')}">新增订单</a>
                                <a href="{:Url('orders/index')}" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-inline marginTop10">
                        <div class="col-lg-5">
                            <div class="form-group">
                                <label class="control-label" for="projectNameInput">搜索条件 :</label>
                                <input name="projectNameInput" id="projectNameInput" class="ipt form-control" data-toggle="tooltip" data-placement="top" title="单号 / 公司名称 / 客户名称">
                                <button type="button" class="btn btn-primary" id="searchprojectName">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover syc-table">
                            <thead>
                            <tr>
                                <th>销售单号</th>
                                <th>公司名称</th>
                                <th>客户名称</th>
                                <th>订单数量</th>
                                <th>订单优惠</th>
                                <th>订单总额</th>
                                <th>已收订金</th>
                                <th>已收余款</th>
                                <th>
                                    <div class="dropdown" id="DropdownAffirm">
                                        <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>客户确认</span><span class="title"></span><span class="caret"></span></a>
                                        <ul class="dropdown-menu aliyun-console-table-search-list">
                                            <li>
                                                <a href="{:Url('orders/index')}">
                                                    <span>全部</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('orders/index',['m'=>'affirm','k'=>'0'])}" id="affirm_0"><span>未确认</span></a>
                                            </li>
                                            <li>
                                                <a href="{:Url('orders/index',['m'=>'affirm','k'=>'1'])}" id="affirm_1"><span>已确认</span></a>
                                            </li>
                                        </ul>
                                    </div>
                                </th>
                                <th>
                                    <div class="dropdown" id="DropdownStasus">
                                    <a class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span>订单状况</span><span class="title"></span><span class="caret"></span></a>
                                    <ul class="dropdown-menu aliyun-console-table-search-list">
                                        <li>
                                            <a href="{:Url('orders/index')}">
                                                <span>全部</span></a>
                                        </li>
                                        <li>
                                            <a href="{:Url('orders/index',['m'=>'status','k'=>'0'])}" id="status_0"><span>审核中</span></a>
                                        </li>
                                        <li>
                                            <a href="{:Url('orders/index',['m'=>'status','k'=>'1'])}" id="status_1"><span>已收订</span></a>
                                        </li>
                                        <li>
                                            <a href="{:Url('orders/index',['m'=>'status','k'=>'2'])}" id="status_2"><span>生产中</span></a>
                                        </li>
                                        <li>
                                            <a href="{:Url('orders/index',['m'=>'status','k'=>'3'])}" id="status_3"><span>生产完</span></a>
                                        </li>
                                        <li>
                                            <a href="{:Url('orders/index',['m'=>'status','k'=>'4'])}" id="status_4"><span>待出库</span></a>
                                        </li>
                                        <li>
                                            <a href="{:Url('orders/index',['m'=>'status','k'=>'5'])}" id="status_5"><span>已出库</span></a>
                                        </li>
                                        <li>
                                            <a href="{:Url('orders/index',['m'=>'status','k'=>'-1'])}" id="status_-1"><span>已废除</span></a>
                                        </li>
                                    </ul>
                                </div>

                                </th>
                                <th>销售日期</th>
                                <th>操作</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="vo" empty="$empty"}
                                <tr>
                                    <td>{$vo.pnumber}</td>
                                    <td>{$vo.pcus_id.cus_name}</td>
                                    <td><a href="{:Url('customers/view',['id'=>$vo.pcus_id.cus_id])}">{$vo.pcsname}</a></td>
                                    <td>{$vo.pcount}</td>
                                    <td>{$vo.pyouhui}</td>
                                    <td>￥{$vo.pamount|number_format=2}</td>
                                    <td>{$vo.amo_dj}</td>
                                    <td>{$vo.amo_yk}</td>
                                    <td>
                                        {eq name="$vo.affirm" value="0"}
                                        <span class="label label-sm label-default">未确认</span>
                                        {else/}
                                        <span class="label label-sm label-success">已确认</span>
                                        {/eq}
                                    </td>
                                    <td>{:purchase_status($vo.status)}</td>
                                    <td>{$vo.pstart_date}</td>
                                    <td>
                                        <a href="{:Url('orders/view',['pid'=>$vo.pnumber])}" target="_blank">查看</a>
                                        <span class="text-explode">|</span>
                                        {if condition="($vo.affirm eq 0)"}
                                        {eq name="$vo.status" value="-1"}
                                        <a href="javascript:void(0);" onclick="huifuLogisticsOne('{$vo.pid}');">恢复</a>
                                        <span class="text-explode">|</span>
                                        <a href="javascript:void(0);" onclick="deleteOrdersOne('{$vo.pnumber}');">删除</a>
                                        {else/}
                                        <a href="{:Url('orders/edit',['pid'=>$vo.pnumber])}">修改</a>
                                        <span class="text-explode">|</span>
                                        <a href="javascript:void(0);" onclick="scrapOrdersOne('{$vo.pid}');">废除</a>
                                        {/eq}
                                        {elseif condition="($vo.status eq 0)"}
                                        <code>等待客户订金</code>
                                        {elseif condition="($vo.status eq 1)"}
                                        <code>在生产排单中</code>
                                        {elseif condition="($vo.status eq 2)"}
                                        {:CountDownDays($vo.pend_date)}
                                        {elseif condition="($vo.status eq 3) AND ($vo.pshoudj == 1)"}
                                        <code>等待客户尾款</code>
                                        {elseif condition="($vo.status eq 3) AND ($vo.pshoudj > 1)"}
                                        <code>客户已付余款</code>
                                        {elseif condition="($vo.status eq 4)"}
                                        <code>已付款待出库</code>
                                        {elseif condition="($vo.status eq 5)"}
                                        <code>{$vo.pend_date}</code>
                                        {/if}
                                    </td>
                                </tr>
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="12">
                                    <div class="pull-left">
                                        </button>
                                    </div>
                                    <div class="pull-right page-box">{$page}</div>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="alert alert-tip"><b>销售订单说明：</b>
                            <p></p>
                            <p>销售订单一旦进入客户确认或审核完毕，将可不在操作此订单，也不可废除，但可以查看。</p>
                            <p>客户确认订单后，会进入财务审核是否已打订金，此时只有财务人员可操作。</p>
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
<script type="text/javascript">
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-sales").addClass("sidebar-nav-active"); // 大分类
        $("#sidebar-orders").addClass("active"); // 小分类

        $('[data-toggle="tooltip"]').tooltip(); //工具提示

        // 单号搜索
        $("#searchprojectName").on('click keyup', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            //var patrn = /^\+?[0-9]*$/;　　//判断是否为正整数 patrn.exec(NameInput) == null
            if (NameInput.length < '2' || NameInput.length > '16') {
                toastr.warning('请输2-16个字符');
                return false
            } else {
                window.location.href="{:Url('orders/index',['m'=>'pnumber'])}?k="+NameInput;
            }
        });

        //查询条件显示
        var sName = '{$Request.param.m}';
        var kName = '{$Request.param.k}';
        if (sName!== '') {
            //客户确认搜索显示 DropdownAffirm
            if (sName == 'affirm') {
                var id='DropdownAffirm';
                listDropdownSearch({i:id,k:kName,s:sName});
            }

            if (sName == 'status') {
                //订单状况搜索显示 DropdownStasus
                var id='DropdownStasus';
                listDropdownSearch({i:id,k:kName,s:sName});
                //console.log(sName)
            }
        }

        // 使用prop实现全选和反选
        $("#ckSelectAll").on('click', function () {
            $("input[name='ckbox[]']").prop("checked", $(this).prop("checked"));
        });
        // 获取选中元素
        $("#DelAllAttr").on('click', function () {
            layui.use(['layer'], function() {
                var layer = layui.layer;
                layer.open({
                    title: '温馨提示',
                    content: '是否要废除所有选择的订单？',
                    btn: ['我已确认', '放弃操作'],
                    yes: function(index, layero){
                        layer.close(index);
                        var valArr = new Array;
                        $("input[name='ckbox[]']:checked").each(function(i){
                            valArr[i] = $(this).val();
                        });
                        if (valArr.length !== 0 && valArr !== null && valArr !== '') {
                            var data={name:'delallattr',uid:valArr.join(',')};
                            $.sycToAjax("{:Url('orders/scrap')}", data);
                        };
                        return false;
                    }
                    ,btn2: function(index, layero){
                        layer.close(index);
                    }
                });
            });
        });
    });
    //单条废弃订单操作
    function scrapOrdersOne(e) {
        if(confirm("是否废除此订单？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',pid:e};
                $.sycToAjax("{:Url('orders/scrap')}", data);
            }
        };
        return false;
    }
    
    //单条恢复订单操作
    function huifuLogisticsOne(e) {
        if(confirm("确定恢复此订单？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',pid:e};
                $.sycToAjax("{:Url('orders/huifu')}", data);
            }
        };
        return false;
    }

    //单条删除订单操作
    function deleteOrdersOne(e) {
        if(confirm("确定删除订单？")){
            if (!isNaN(e) && e !== null && e !== '') {
                var data={name:'scrap',pid:e};
                $.sycToAjax("{:Url('orders/delete')}", data);
            }
        };
        return false;
    }
</script>
</body>
</html>