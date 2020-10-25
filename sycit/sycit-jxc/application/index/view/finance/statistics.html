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
                    <div class="product-nav-title">销售统计</div>
                    <div class="ng-isolate-scope"></div>
                    <div class="product-nav-list">
                        <ul>
                            <li id="index">
                                <div class="ng-isolate-scope"><a href="{:Url('finance/statistics')}">
                                    <div class="nav-icon"></div><div class="nav-title">全部</div>
                                </a></div>
                            </li>
                            <li id="week">
                                <div class="ng-isolate-scope"><a href="{:Url('finance/statistics',['q'=>'week'])}">
                                    <div class="nav-icon"></div><div class="nav-title">本周销售</div>
                                </a></div>
                            </li>
                            <li id="month">
                                <div class="ng-isolate-scope"><a href="{:Url('finance/statistics',['q'=>'month'])}">
                                    <div class="nav-icon"></div><div class="nav-title">本月销售</div>
                                </a></div>
                            </li>
                            <li id="lastmonth">
                                <div class="ng-isolate-scope"><a href="{:Url('finance/statistics',['q'=>'lastmonth'])}">
                                    <div class="nav-icon"></div><div class="nav-title">上月销售</div>
                                </a></div>
                            </li>
                            <li id="year">
                                <div class="ng-isolate-scope"><a href="{:Url('finance/statistics',['q'=>'year'])}">
                                    <div class="nav-icon"></div><div class="nav-title">今年销售</div>
                                </a></div>
                            </li>
                            <li id="lastyear">
                                <div class="ng-isolate-scope"><a href="{:Url('finance/statistics',['q'=>'lastyear'])}">
                                    <div class="nav-icon"></div><div class="nav-title">去年销售</div>
                                </a></div>
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
                                <h5>
                                    <span>{$title}</span>
                                    <span class="text-explode">|</span>
                                    <span id="title-name" style="color:#09c"></span>
                                </h5>
                            </div>
                            <div class="pull-right">
                                <a href="{:Url('finance/statistics')}" class="btn btn-default">
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
                                <input name="projectNameInput" id="projectNameInput" class="ipt form-control" data-toggle="tooltip" data-placement="top" title="单号 / 客户名称">
                                <button type="button" class="btn btn-primary" id="searchprojectName">搜索</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <table class="table table-hover syc-table border">
                            <thead>
                            <tr>
                                <th rowspan="2" width="80">销售单号</th>
                                <th rowspan="2" width="80">客户名称</th>
                                <th rowspan="2" width="60">总数量</th>
                                <th rowspan="2">实收金额</th>
                                <th rowspan="2">出库日期</th>
                                <th rowspan="2" style="border-right: 2px solid #e1e6eb;">周期</th>
                                <th rowspan="2" class="w80">颜色</th>
                                <th rowspan="2" colspan="2" class="w80">产品编号</th>
                                <th colspan="3" class="w150">规格/mm</th>
                                <th rowspan="2" class="w50">吊脚高度/mm</th>
                                <th rowspan="2" class="w65" colspan="2">包边线设置</th>
                                <th rowspan="2" class="w80">锁向</th>
                                <th rowspan="2" class="w80">锁具</th>
                                <th rowspan="2" class="w65">数量</th>
                            </tr>
                            <tr>
                                <th class="w50">宽</th>
                                <th class="w50">高</th>
                                <th class="w50">厚</th>
                            </tr>
                            </thead>
                            <tbody>
                            {volist name="list" id="vo" empty="$empty" key="k"}
                            {eq name="$vo.count" value="1"}
                            <tr>
                                <td><a href="{:Url('schedule/delivery',['pid'=>$vo.pnumber])}" target="_blank">{$vo.pnumber}</a></td>
                                <td>{$vo.pcsname}</td>
                                <td>{$vo.pcount}</td>
                                <td>{$vo.allamo}</td>
                                <td>{$vo.pend_date}</td>
                                <td style="border-right: 2px solid #ff8100;">{:Shengchanzq($vo.pstart_date,$vo.pend_date)}</td>

                                {volist name="$vo.sun" id="sun"}
                                <td>{$sun.yanse}</td>
                                <td>{$sun.products}</td>
                                <td>{$sun.chanph}</td>
                                <td>{$sun.breadth}</td>
                                <td>{$sun.heiget}</td>
                                <td>{$sun.thick}</td>
                                <td>{$sun.diaojiao}</td>
                                <td width="35">{$sun.attribute}</td>
                                <td width="65">{$sun.baobian}</td>
                                <td>{$sun.suoxiang}</td>
                                <td>{$sun.fittings}</td>
                                <td>{$sun.quantity}</td>
                                {/volist}

                            </tr>
                            {else/}
                            <tr>
                                <td rowspan="{php}echo $vo['count']+1;{/php}"><a href="{:Url('schedule/delivery',['pid'=>$vo.pnumber])}" target="_blank">{$vo.pnumber}</a></td>
                                <td rowspan="{php}echo $vo['count']+1;{/php}">{$vo.pcsname}</td>
                                <td rowspan="{php}echo $vo['count']+1;{/php}">{$vo.pcount}</td>
                                <td rowspan="{php}echo $vo['count']+1;{/php}">{$vo.allamo}</td>
                                <td rowspan="{php}echo $vo['count']+1;{/php}">{$vo.pend_date}</td>
                                <td rowspan="{php}echo $vo['count']+1;{/php}" style="border-right: 2px solid #ff8100;">{:Shengchanzq($vo.pstart_date,$vo.pend_date)}</td>
                            </tr>
                            {volist name="$vo.sun" id="sun"}
                            <tr>
                                <td>{$sun.yanse}</td>
                                <td>{$sun.products}</td>
                                <td>{$sun.chanph}</td>
                                <td>{$sun.breadth}</td>
                                <td>{$sun.heiget}</td>
                                <td>{$sun.thick}</td>
                                <td>{$sun.diaojiao}</td>
                                <td width="35">{$sun.attribute}</td>
                                <td width="65">{$sun.baobian}</td>
                                <td>{$sun.suoxiang}</td>
                                <td>{$sun.fittings}</td>
                                <td>{$sun.quantity}</td>
                            </tr>
                            {/volist}

                            {/eq}
                            {/volist}
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan="4"><span>总订单：{$pcount}，</span><span style="margin-left: 5px;">总实收：{$psum}</span></td>
                                <td colspan="14">
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
        $("#sidebar-finance").addClass("sidebar-nav-active"); // 大分类
        $("#finance-statistics").addClass("active"); // 小分类

        $('[data-toggle="tooltip"]').tooltip(); //工具提示

        //查询条件显示
        var qName = '{$Request.param.q}';

        switch (qName) {
            case 'week':
                $("#title-name").empty().text("本周销售");
                $("#week").addClass('active');
                break;
            case 'month':
                $("#title-name").empty().text("本月销售");
                $("#month").addClass('active');
                break;
            case 'lastmonth':
                $("#title-name").empty().text("上月销售");
                $("#lastmonth").addClass('active');
                break;
            case 'year':
                $("#title-name").empty().text("今年销售");
                $("#year").addClass('active');
                break;
            case 'lastyear':
                $("#title-name").empty().text("去年销售");
                $("#lastyear").addClass('active');
                break;
            default:
                $("#title-name").empty().text("全部");
                $("#index").addClass('active');
                break;
        }

        // 单号搜索
        $("#searchprojectName").on('click keyup', function () {
            var NameInput = $("input[name='projectNameInput']").val();
            //var patrn = /^\+?[0-9]*$/;　　//判断是否为正整数 patrn.exec(NameInput) == null
            if (NameInput.length < '2' || NameInput.length > '16') {
                toastr.warning('请输2-16个字符');
                return false
            } else {
                window.location.href="{:Url('finance/statistics',['m'=>'pnumber'])}?k="+NameInput;
            }
        });
    });
</script>
</body>
</html>